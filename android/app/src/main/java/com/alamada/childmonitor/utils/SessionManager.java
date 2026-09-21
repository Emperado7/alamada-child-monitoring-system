package com.alamada.childmonitor.utils;

import android.content.Context;
import android.content.SharedPreferences;

/**
 * Manages the authenticated user session using SharedPreferences.
 * Stores the Sanctum API token and basic user info locally.
 */
public class SessionManager {

    private static final String PREF_NAME   = "AlamadaSession";
    private static final String KEY_TOKEN   = "api_token";
    private static final String KEY_USER_ID = "user_id";
    private static final String KEY_NAME    = "user_name";
    private static final String KEY_EMAIL   = "user_email";
    private static final String KEY_ROLE    = "user_role";

    private final SharedPreferences prefs;
    private final SharedPreferences.Editor editor;

    public SessionManager(Context context) {
        prefs  = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = prefs.edit();
    }

    // ── Save / clear ──────────────────────────────────────────────

    public void saveSession(String token, int userId, String name, String email, String role) {
        editor.putString(KEY_TOKEN,   token);
        editor.putInt(KEY_USER_ID,    userId);
        editor.putString(KEY_NAME,    name);
        editor.putString(KEY_EMAIL,   email);
        editor.putString(KEY_ROLE,    role);
        editor.apply();
    }

    public void clearSession() {
        editor.clear().apply();
    }

    // ── Getters ───────────────────────────────────────────────────

    public boolean isLoggedIn() {
        return prefs.getString(KEY_TOKEN, null) != null;
    }

    public String getToken() {
        return prefs.getString(KEY_TOKEN, null);
    }

    public int getUserId() {
        return prefs.getInt(KEY_USER_ID, -1);
    }

    public String getUserName() {
        return prefs.getString(KEY_NAME, "");
    }

    public String getUserEmail() {
        return prefs.getString(KEY_EMAIL, "");
    }

    public String getUserRole() {
        return prefs.getString(KEY_ROLE, "");
    }

    /** Returns "Bearer <token>" for use in Authorization headers */
    public String getBearerToken() {
        return "Bearer " + getToken();
    }
}
