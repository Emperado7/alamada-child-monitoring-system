package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Toast;

import com.alamada.childmonitor.databinding.ActivitySettingsBinding;
import com.alamada.childmonitor.models.MessageResponse;
import com.alamada.childmonitor.network.ApiClient;

import java.util.HashMap;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class SettingsActivity extends BaseActivity {

    private ActivitySettingsBinding binding;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivitySettingsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        // Populate user info
        String name = session.getUserName();
        binding.tvName.setText(name != null && !name.isEmpty() ? name : "Parent");

        // ── Bottom nav ────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this, DashboardActivity.class));
            finish();
        });
        binding.tabChild.setOnClickListener(v -> {
            startActivity(new Intent(this, ChildDetailActivity.class));
            finish();
        });
        binding.tabNotifications.setOnClickListener(v -> {
            startActivity(new Intent(this, NotificationsActivity.class));
            finish();
        });

        // ── Menu cards ────────────────────────────────────
        binding.cardProfile.setOnClickListener(v -> showProfileDialog());
        binding.cardSecurity.setOnClickListener(v -> showSecurityDialog());
        binding.cardNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));

        // ── Logout ────────────────────────────────────────
        binding.cardLogout.setOnClickListener(v -> confirmLogout());
    }

    // ── Profile dialog ────────────────────────────────────

    private void showProfileDialog() {
        new android.app.AlertDialog.Builder(this)
            .setTitle("My Profile")
            .setMessage(
                "Name:   " + session.getUserName() + "\n" +
                "Email:  " + session.getUserEmail() + "\n" +
                "Role:    Parent/Guardian\n\n" +
                "Contact the learning center administrator\n" +
                "to update your profile information."
            )
            .setPositiveButton("OK", null)
            .show();
    }

    // ── Change-password dialog ────────────────────────────

    private void showSecurityDialog() {
        android.view.LayoutInflater inflater = android.view.LayoutInflater.from(this);
        android.view.View view = inflater.inflate(
            com.alamada.childmonitor.R.layout.activity_security, null);

        android.app.AlertDialog dialog = new android.app.AlertDialog.Builder(this)
            .setView(view).create();

        com.google.android.material.textfield.TextInputEditText etCurrent =
            view.findViewById(com.alamada.childmonitor.R.id.et_current_password);
        com.google.android.material.textfield.TextInputEditText etNew =
            view.findViewById(com.alamada.childmonitor.R.id.et_new_password);
        com.google.android.material.textfield.TextInputEditText etConfirm =
            view.findViewById(com.alamada.childmonitor.R.id.et_confirm_password);

        android.view.View btnBack = view.findViewById(com.alamada.childmonitor.R.id.btn_back);
        if (btnBack != null) btnBack.setOnClickListener(v -> dialog.dismiss());

        com.google.android.material.button.MaterialButton btnUpdate =
            view.findViewById(com.alamada.childmonitor.R.id.btn_update_password);
        if (btnUpdate != null) {
            btnUpdate.setOnClickListener(v -> {
                String current = etCurrent != null && etCurrent.getText() != null
                    ? etCurrent.getText().toString().trim() : "";
                String newPass = etNew != null && etNew.getText() != null
                    ? etNew.getText().toString().trim() : "";
                String confirm = etConfirm != null && etConfirm.getText() != null
                    ? etConfirm.getText().toString().trim() : "";

                if (current.isEmpty() || newPass.isEmpty() || confirm.isEmpty()) {
                    Toast.makeText(this, "Please fill in all fields.", Toast.LENGTH_SHORT).show();
                    return;
                }
                if (!newPass.equals(confirm)) {
                    Toast.makeText(this, "Passwords do not match.", Toast.LENGTH_SHORT).show();
                    return;
                }
                if (newPass.length() < 8) {
                    Toast.makeText(this, "Password must be at least 8 characters.", Toast.LENGTH_SHORT).show();
                    return;
                }

                Map<String, String> body = new HashMap<>();
                body.put("current_password", current);
                body.put("new_password", newPass);
                body.put("new_password_confirmation", confirm);

                ApiClient.getInstance(session.getBearerToken())
                    .changePassword(body)
                    .enqueue(new Callback<MessageResponse>() {
                        @Override
                        public void onResponse(Call<MessageResponse> call,
                                               Response<MessageResponse> response) {
                            if (response.isSuccessful()) {
                                Toast.makeText(SettingsActivity.this,
                                    "Password updated successfully.", Toast.LENGTH_SHORT).show();
                                dialog.dismiss();
                            } else if (response.code() == 401) {
                                dialog.dismiss();
                                handleUnauthorised();
                            } else {
                                Toast.makeText(SettingsActivity.this,
                                    "Current password is incorrect.", Toast.LENGTH_SHORT).show();
                            }
                        }
                        @Override
                        public void onFailure(Call<MessageResponse> call, Throwable t) {
                            Toast.makeText(SettingsActivity.this,
                                "Network error.", Toast.LENGTH_SHORT).show();
                        }
                    });
            });
        }
        dialog.show();
    }

    // ── Logout confirmation ───────────────────────────────

    private void confirmLogout() {
        new android.app.AlertDialog.Builder(this)
            .setTitle("Log Out")
            .setMessage("Are you sure you want to log out?")
            .setPositiveButton("Log Out", (d, w) -> doLogout())
            .setNegativeButton("Cancel", null)
            .show();
    }
}
