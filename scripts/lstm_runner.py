import argparse
import json
import os
import time
from datetime import datetime

import numpy as np
import pandas as pd
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.preprocessing import MinMaxScaler
import tensorflow as tf
from tensorflow.keras import layers, models


def parse_args():
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", required=True)
    parser.add_argument("--output", required=True)
    return parser.parse_args()


def load_input(path):
    with open(path, "r", encoding="utf-8") as f:
        return json.load(f)


def health_indicator(pm25):
    if pm25 is None:
        return None
    if pm25 <= 15.5:
        return "Baik"
    if pm25 <= 55.4:
        return "Sedang"
    if pm25 <= 150.4:
        return "Tidak Sehat"
    if pm25 <= 250.4:
        return "Sangat Tidak Sehat"
    return "Berbahaya"


def create_sequences(features, target, lookback=48):
    X, y = [], []
    for i in range(len(features) - lookback):
        X.append(features[i : i + lookback])
        y.append(target[i + lookback])
    return np.array(X), np.array(y)


def winsorize_iqr(series):
    q1 = series.quantile(0.25)
    q3 = series.quantile(0.75)
    iqr = q3 - q1
    lower = q1 - 1.5 * iqr
    upper = q3 + 1.5 * iqr
    return series.clip(lower=lower, upper=upper), float(lower), float(upper)


def run_bivariate_lstm(train_df, test_df, feature_cols, target_col, lookback=48, epochs=100, batch_size=32):
    feature_scaler = MinMaxScaler()
    train_features = feature_scaler.fit_transform(train_df[feature_cols].values)
    test_features = feature_scaler.transform(test_df[feature_cols].values)

    target_scaler = MinMaxScaler()
    train_target = target_scaler.fit_transform(train_df[[target_col]].values)
    test_target = target_scaler.transform(test_df[[target_col]].values)

    X_train, y_train = create_sequences(train_features, train_target, lookback)
    X_test, y_test = create_sequences(test_features, test_target, lookback)

    if X_train.shape[0] == 0 or X_test.shape[0] == 0:
        raise RuntimeError(f"Sequence kosong untuk target {target_col}")

    model = models.Sequential(
        [
            layers.Input(shape=(lookback, X_train.shape[2])),
            layers.LSTM(64, activation="tanh"),
            layers.Dropout(0.2),
            layers.Dense(1),
        ]
    )
    model.compile(optimizer="adam", loss="mse", metrics=["mae"])

    es = tf.keras.callbacks.EarlyStopping(monitor="val_loss", patience=10, restore_best_weights=True)
    history = model.fit(
        X_train,
        y_train,
        validation_split=0.2,
        epochs=epochs,
        batch_size=batch_size,
        callbacks=[es],
        verbose=0,
    )

    y_pred_scaled = model.predict(X_test, verbose=0)
    y_test_inv = target_scaler.inverse_transform(y_test.reshape(-1, 1)).flatten()
    y_pred_inv = target_scaler.inverse_transform(y_pred_scaled).flatten()

    mse = mean_squared_error(y_test_inv, y_pred_inv)
    mae = mean_absolute_error(y_test_inv, y_pred_inv)
    rmse = float(np.sqrt(mse))
    r2 = r2_score(y_test_inv, y_pred_inv)

    result_df = pd.DataFrame(
        {
            "datetime": test_df.index[lookback:],
            "actual": y_test_inv,
            "predicted": y_pred_inv,
        }
    )

    return {
        "mae": float(mae),
        "mse": float(mse),
        "rmse": rmse,
        "r2": float(r2),
        "result_df": result_df,
        "history": history.history,
        "model": model,
        "feature_scaler": feature_scaler,
        "target_scaler": target_scaler,
        "feature_cols": feature_cols,
        "target_col": target_col,
    }


def predict_next(model_pack, history_df, lookback):
    feature_cols = model_pack["feature_cols"]
    scaler_x = model_pack["feature_scaler"]
    scaler_y = model_pack["target_scaler"]
    model = model_pack["model"]

    window = history_df[feature_cols].tail(lookback).values
    X = scaler_x.transform(window)
    X = X.reshape(1, lookback, len(feature_cols))
    pred_scaled = model.predict(X, verbose=0)
    pred = scaler_y.inverse_transform(pred_scaled).flatten()[0]
    return float(pred)


def main():
    args = parse_args()
    payload = load_input(args.input)

    t0 = time.time()
    rows = payload.get("rows", [])
    if not rows:
        raise RuntimeError("Input rows kosong")

    df = pd.DataFrame(rows)
    df["datetime"] = pd.to_datetime(df["datetime"])
    df = df.sort_values("datetime").set_index("datetime")
    df = df[["pm10", "pm25"]].copy()

    idx = pd.date_range(df.index.min(), df.index.max(), freq="30min")
    df = df.reindex(idx)

    pm10_zero_to_null = int((df["pm10"] == 0).sum(skipna=True))
    pm25_zero_to_null = int((df["pm25"] == 0).sum(skipna=True))
    df["pm10"] = df["pm10"].replace(0, np.nan)
    df["pm25"] = df["pm25"].replace(0, np.nan)

    pm10_null_before = int(df["pm10"].isna().sum())
    pm25_null_before = int(df["pm25"].isna().sum())

    prep_start = time.time()
    df["pm10"] = df["pm10"].interpolate(method="linear").bfill().ffill()
    df["pm25"] = df["pm25"].interpolate(method="linear").bfill().ffill()
    df["pm10"], pm10_lb, pm10_ub = winsorize_iqr(df["pm10"])
    df["pm25"], pm25_lb, pm25_ub = winsorize_iqr(df["pm25"])
    prep_duration = max(1, int(time.time() - prep_start))

    split = payload.get("split", {})
    train_start = pd.to_datetime(split.get("train_start"))
    train_end = pd.to_datetime(split.get("train_end"))
    test_start = pd.to_datetime(split.get("test_start"))
    test_end = pd.to_datetime(split.get("test_end"))

    train_df = df.loc[(df.index >= train_start) & (df.index <= train_end)].copy()
    test_df = df.loc[(df.index >= test_start) & (df.index <= test_end)].copy()

    if train_df.shape[0] < 100 or test_df.shape[0] < 100:
        split_idx = int(len(df) * 0.8)
        train_df = df.iloc[:split_idx].copy()
        test_df = df.iloc[split_idx:].copy()

    lookback = int(payload.get("lookback", 48))

    train_start_t = time.time()
    result_pm10 = run_bivariate_lstm(
        train_df=train_df,
        test_df=test_df,
        feature_cols=["pm10", "pm25"],
        target_col="pm10",
        lookback=lookback,
    )
    result_pm25 = run_bivariate_lstm(
        train_df=train_df,
        test_df=test_df,
        feature_cols=["pm25", "pm10"],
        target_col="pm25",
        lookback=lookback,
    )
    train_duration = max(1, int(time.time() - train_start_t))

    eval_pm10 = result_pm10["result_df"].copy().rename(
        columns={"actual": "pm10_actual", "predicted": "pm10_predicted"}
    )
    eval_pm25 = result_pm25["result_df"].copy().rename(
        columns={"actual": "pm25_actual", "predicted": "pm25_predicted"}
    )
    eval_df = pd.merge(eval_pm10, eval_pm25, on="datetime", how="outer").sort_values("datetime")

    preds = []
    idx_counter = 1
    for _, row in eval_df.iterrows():
        pm25_pred = row.get("pm25_predicted")
        pm25_pred_val = None if pd.isna(pm25_pred) else float(pm25_pred)
        preds.append(
            {
                "predicted_for": pd.Timestamp(row["datetime"]).strftime("%Y-%m-%d %H:%M:%S"),
                "horizon_index": idx_counter,
                "pm10_actual": None if pd.isna(row.get("pm10_actual")) else round(float(row["pm10_actual"]), 4),
                "pm10_predicted": None if pd.isna(row.get("pm10_predicted")) else round(float(row["pm10_predicted"]), 4),
                "pm25_actual": None if pd.isna(row.get("pm25_actual")) else round(float(row["pm25_actual"]), 4),
                "pm25_predicted": None if pd.isna(pm25_pred) else round(float(pm25_pred), 4),
                "health_indicator": health_indicator(pm25_pred_val),
            }
        )
        idx_counter += 1

    full_pm10 = run_bivariate_lstm(
        train_df=df,
        test_df=df.iloc[-max(lookback + 24, 120) :].copy(),
        feature_cols=["pm10", "pm25"],
        target_col="pm10",
        lookback=lookback,
        epochs=40,
    )
    full_pm25 = run_bivariate_lstm(
        train_df=df,
        test_df=df.iloc[-max(lookback + 24, 120) :].copy(),
        feature_cols=["pm25", "pm10"],
        target_col="pm25",
        lookback=lookback,
        epochs=40,
    )

    forecast_window = payload.get("forecast", {})
    forecast_start = pd.to_datetime(forecast_window.get("start"))
    forecast_end = pd.to_datetime(forecast_window.get("end"))

    history_df = df.copy()
    cursor = forecast_start
    gen_start = time.time()
    while cursor <= forecast_end:
        pm10_pred = predict_next(full_pm10, history_df, lookback)
        pm25_pred = predict_next(full_pm25, history_df, lookback)

        preds.append(
            {
                "predicted_for": cursor.strftime("%Y-%m-%d %H:%M:%S"),
                "horizon_index": idx_counter,
                "pm10_actual": None,
                "pm10_predicted": round(pm10_pred, 4),
                "pm25_actual": None,
                "pm25_predicted": round(pm25_pred, 4),
                "health_indicator": health_indicator(pm25_pred),
            }
        )
        idx_counter += 1
        history_df.loc[cursor, "pm10"] = pm10_pred
        history_df.loc[cursor, "pm25"] = pm25_pred
        cursor += pd.Timedelta(minutes=30)
    gen_duration = max(1, int(time.time() - gen_start))

    output = {
        "metrics": {
            "pm10": {
                "mae": round(result_pm10["mae"], 6),
                "mse": round(result_pm10["mse"], 6),
                "rmse": round(result_pm10["rmse"], 6),
                "r2": round(result_pm10["r2"], 6),
            },
            "pm25": {
                "mae": round(result_pm25["mae"], 6),
                "mse": round(result_pm25["mse"], 6),
                "rmse": round(result_pm25["rmse"], 6),
                "r2": round(result_pm25["r2"], 6),
            },
        },
        "predictions": preds,
        "steps": [
            {
                "step_order": 1,
                "step_key": "preprocessing",
                "status": "success",
                "duration_seconds": prep_duration,
                "summary": {
                    "missing_pm10": pm10_null_before,
                    "missing_pm25": pm25_null_before,
                    "zero_pm10_to_null": pm10_zero_to_null,
                    "zero_pm25_to_null": pm25_zero_to_null,
                    "winsor_pm10": [round(pm10_lb, 4), round(pm10_ub, 4)],
                    "winsor_pm25": [round(pm25_lb, 4), round(pm25_ub, 4)],
                },
            },
            {
                "step_order": 2,
                "step_key": "scaling",
                "status": "success",
                "duration_seconds": 1,
                "summary": {"method": "minmax"},
            },
            {
                "step_order": 3,
                "step_key": "windowing",
                "status": "success",
                "duration_seconds": 1,
                "summary": {
                    "lookback": lookback,
                    "train_rows": int(train_df.shape[0]),
                    "test_rows": int(test_df.shape[0]),
                },
            },
            {
                "step_order": 4,
                "step_key": "training",
                "status": "success",
                "duration_seconds": train_duration,
                "summary": {
                    "status": "Trained",
                    "training_loss": float(result_pm10["history"]["loss"][-1]),
                    "validation_loss": float(result_pm10["history"]["val_loss"][-1]),
                },
            },
            {
                "step_order": 5,
                "step_key": "generate",
                "status": "success",
                "duration_seconds": gen_duration,
                "summary": {
                    "jumlah_titik_prediksi": len(preds),
                    "timestamp_run": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                    "periode_evaluasi": f"{test_df.index.min().strftime('%Y-%m-%d %H:%M:%S')} s.d {test_df.index.max().strftime('%Y-%m-%d %H:%M:%S')}",
                },
            },
            {
                "step_order": 6,
                "step_key": "evaluation",
                "status": "success",
                "duration_seconds": 1,
                "summary": {
                    "pm10_r2": round(result_pm10["r2"], 6),
                    "pm25_r2": round(result_pm25["r2"], 6),
                },
            },
        ],
        "meta": {
            "total_seconds": int(time.time() - t0),
            "rows": int(df.shape[0]),
            "lookback": lookback,
        },
    }

    os.makedirs(os.path.dirname(args.output), exist_ok=True)
    with open(args.output, "w", encoding="utf-8") as f:
        json.dump(output, f, ensure_ascii=False)


if __name__ == "__main__":
    main()

