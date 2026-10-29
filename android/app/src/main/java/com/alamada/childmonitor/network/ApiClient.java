package com.alamada.childmonitor.network;

import com.alamada.childmonitor.BuildConfig;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

import java.util.Objects;
import java.util.concurrent.TimeUnit;

/**
 * Singleton Retrofit client.
 * Call ApiClient.init(token) once after login, then ApiClient.get() everywhere.
 */
public class ApiClient {

    private static volatile ApiService instance;
    private static String currentToken;

    private ApiClient() {}

    /** Call after login or when the token changes. */
    public static synchronized void init(String bearerToken) {
        // Use Objects.equals so null-safe — bearerToken can be null for unauthenticated calls
        if (instance == null || !Objects.equals(bearerToken, currentToken)) {
            currentToken = bearerToken;
            instance = buildService(bearerToken);
        }
    }

    /** Returns the singleton ApiService. Call init() first. */
    public static ApiService get() {
        if (instance == null) {
            throw new IllegalStateException("ApiClient not initialised — call ApiClient.init(token) first.");
        }
        return instance;
    }

    /** Convenience — matches old call-site signature so existing code needs minimal changes. */
    public static ApiService getInstance(String bearerToken) {
        init(bearerToken);
        return instance;
    }

    /** Invalidate after logout so a stale token can't be reused. */
    public static synchronized void clear() {
        instance = null;
        currentToken = null;
    }

    // ── Private builder ───────────────────────────────────────────

    private static ApiService buildService(String bearerToken) {
        HttpLoggingInterceptor logging = new HttpLoggingInterceptor();
        logging.setLevel(BuildConfig.DEBUG
                ? HttpLoggingInterceptor.Level.BODY
                : HttpLoggingInterceptor.Level.NONE);

        OkHttpClient client = new OkHttpClient.Builder()
                .connectTimeout(90, TimeUnit.SECONDS)
                .readTimeout(90, TimeUnit.SECONDS)
                .writeTimeout(90, TimeUnit.SECONDS)
                .addInterceptor(logging)
                .addInterceptor(chain -> {
                    okhttp3.Request original = chain.request();
                    okhttp3.Request.Builder builder = original.newBuilder()
                            .header("Accept", "application/json")
                            .header("Content-Type", "application/json");
                    if (bearerToken != null && !bearerToken.isEmpty()) {
                        builder.header("Authorization", bearerToken);
                    }
                    return chain.proceed(builder.build());
                })
                .build();

        return new Retrofit.Builder()
                .baseUrl(BuildConfig.BASE_URL)
                .client(client)
                .addConverterFactory(GsonConverterFactory.create())
                .build()
                .create(ApiService.class);
    }
}
