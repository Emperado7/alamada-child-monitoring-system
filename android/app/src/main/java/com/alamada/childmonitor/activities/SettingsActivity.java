package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;

import androidx.appcompat.app.AppCompatActivity;

import com.alamada.childmonitor.databinding.ActivitySettingsBinding;
import com.alamada.childmonitor.utils.SessionManager;

public class SettingsActivity extends AppCompatActivity {

    private ActivitySettingsBinding binding;
    private SessionManager session;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivitySettingsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        session = new SessionManager(this);

        // ── Populate user info ────────────────────────────
        String name = session.getUserName();
        binding.tvName.setText(name != null && !name.isEmpty() ? name : "Parent");

        // ── Bottom nav ────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this, DashboardActivity.class));
            finish();
        });

        binding.tabChild.setOnClickListener(v -> {
            startActivity(new Intent(this, ChildDetailActivity.class));
        });

        // ── Menu item cards ───────────────────────────────

        // Profile card → show profile bottom sheet / dialog
        binding.cardProfile.setOnClickListener(v -> {
            showProfileDialog();
        });

        // Security card → password change
        binding.cardSecurity.setOnClickListener(v -> {
            showSecurityDialog();
        });

        // Notifications card → open notifications list
        binding.cardNotifications.setOnClickListener(v -> {
            startActivity(new Intent(this, NotificationsActivity.class));
        });
    }

    // ── Profile info dialog ───────────────────────────────

    private void showProfileDialog() {
        String name  = session.getUserName();
        String email = session.getUserEmail();
        String role  = capitalize(session.getUserRole());

        new android.app.AlertDialog.Builder(this)
            .setTitle("My Profile")
            .setMessage(
                "Name:   " + name + "\n" +
                "Email:  " + email + "\n" +
                "Role:    " + role + "\n\n" +
                "Contact the learning center administrator\n" +
                "to update your profile information."
            )
            .setPositiveButton("OK", null)
            .show();
    }

    // ── Security / change password dialog ────────────────

    private void showSecurityDialog() {
        android.view.LayoutInflater inflater = android.view.LayoutInflater.from(this);
        android.view.View view = inflater.inflate(
            com.alamada.childmonitor.R.layout.activity_security, null);

        android.app.AlertDialog dialog = new android.app.AlertDialog.Builder(this)
            .setView(view)
            .create();

        // Wire up the Update Password button inside the dialog
        com.google.android.material.button.MaterialButton btnUpdate =
            view.findViewById(com.alamada.childmonitor.R.id.btn_update_password);

        com.google.android.material.textfield.TextInputEditText etCurrent =
            view.findViewById(com.alamada.childmonitor.R.id.et_current_password);
        com.google.android.material.textfield.TextInputEditText etNew =
            view.findViewById(com.alamada.childmonitor.R.id.et_new_password);
        com.google.android.material.textfield.TextInputEditText etConfirm =
            view.findViewById(com.alamada.childmonitor.R.id.et_confirm_password);

        // Back button inside dialog view
        android.view.View btnBack = view.findViewById(com.alamada.childmonitor.R.id.btn_back);
        if (btnBack != null) btnBack.setOnClickListener(v2 -> dialog.dismiss());

        if (btnUpdate != null) {
            btnUpdate.setOnClickListener(v -> {
                String current = etCurrent != null && etCurrent.getText() != null
                    ? etCurrent.getText().toString().trim() : "";
                String newPass = etNew != null && etNew.getText() != null
                    ? etNew.getText().toString().trim() : "";
                String confirm = etConfirm != null && etConfirm.getText() != null
                    ? etConfirm.getText().toString().trim() : "";

                if (current.isEmpty() || newPass.isEmpty() || confirm.isEmpty()) {
                    android.widget.Toast.makeText(this,
                        "Please fill in all fields.", android.widget.Toast.LENGTH_SHORT).show();
                    return;
                }
                if (!newPass.equals(confirm)) {
                    android.widget.Toast.makeText(this,
                        "Passwords do not match.", android.widget.Toast.LENGTH_SHORT).show();
                    return;
                }
                if (newPass.length() < 8) {
                    android.widget.Toast.makeText(this,
                        "Password must be at least 8 characters.", android.widget.Toast.LENGTH_SHORT).show();
                    return;
                }

                // Call API
                java.util.Map<String, String> body = new java.util.HashMap<>();
                body.put("current_password", current);
                body.put("new_password", newPass);
                body.put("new_password_confirmation", confirm);

                com.alamada.childmonitor.network.ApiClient
                    .getInstance(session.getBearerToken())
                    .changePassword(body)
                    .enqueue(new retrofit2.Callback<
                            com.alamada.childmonitor.models.MessageResponse>() {
                        @Override
                        public void onResponse(
                            retrofit2.Call<com.alamada.childmonitor.models.MessageResponse> call,
                            retrofit2.Response<com.alamada.childmonitor.models.MessageResponse> response) {
                            if (response.isSuccessful()) {
                                android.widget.Toast.makeText(SettingsActivity.this,
                                    "Password updated successfully.",
                                    android.widget.Toast.LENGTH_SHORT).show();
                                dialog.dismiss();
                            } else if (response.code() == 422) {
                                android.widget.Toast.makeText(SettingsActivity.this,
                                    "Current password is incorrect.",
                                    android.widget.Toast.LENGTH_SHORT).show();
                            } else {
                                android.widget.Toast.makeText(SettingsActivity.this,
                                    "Failed. Try again.",
                                    android.widget.Toast.LENGTH_SHORT).show();
                            }
                        }
                        @Override
                        public void onFailure(
                            retrofit2.Call<com.alamada.childmonitor.models.MessageResponse> call,
                            Throwable t) {
                            android.widget.Toast.makeText(SettingsActivity.this,
                                "Network error.", android.widget.Toast.LENGTH_SHORT).show();
                        }
                    });
            });
        }

        dialog.show();
    }

    private String capitalize(String s) {
        if (s == null || s.isEmpty()) return "Parent";
        return Character.toUpperCase(s.charAt(0)) + s.substring(1);
    }
}
