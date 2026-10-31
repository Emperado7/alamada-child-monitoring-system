package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Toast;

import com.alamada.childmonitor.R;
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
    /** Cached role label — used by both onCreate and showProfileDialog */
    private String roleLabel = "Parent/Guardian";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivitySettingsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        // ── User info ──────────────────────────────────────────────
        String name = session.getUserName();
        binding.tvName.setText(name != null && !name.isEmpty() ? name : "User");

        String role = session.getUserRole();
        if ("admin".equals(role))       roleLabel = "Administrator";
        else if ("staff".equals(role))  roleLabel = "Staff Member";
        else                             roleLabel = "Parent/Guardian";

        binding.tvRoleLabel.setText(roleLabel);

        // Avatar — show first letter of name in green
        if (name != null && !name.isEmpty()) {
            binding.tvAvatar.setText(String.valueOf(name.charAt(0)).toUpperCase());
            binding.tvAvatar.setTextSize(34);
            binding.tvAvatar.setTextColor(
                getResources().getColor(R.color.primary, null));
        }

        // ── Bottom nav ─────────────────────────────────────────────
        boolean isStaff = !"parent".equals(role);
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this,
                isStaff ? StaffDashboardActivity.class : DashboardActivity.class));
            finish();
        });
        binding.tabChild.setOnClickListener(v -> {
            startActivity(new Intent(this,
                isStaff ? StaffChildListActivity.class : ChildDetailActivity.class));
            finish();
        });

        // ── Menu cards ─────────────────────────────────────────────
        binding.cardProfile.setOnClickListener(v -> showProfileDialog());
        binding.cardSecurity.setOnClickListener(v -> showSecurityDialog());
        binding.cardNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));

        // ── Log Out link ───────────────────────────────────────────
        binding.cardLogout.setOnClickListener(v -> confirmLogout());
    }

    // ── Profile dialog ─────────────────────────────────────────────

    private void showProfileDialog() {
        new android.app.AlertDialog.Builder(this)
            .setTitle("My Profile")
            .setMessage(
                "Name:   " + session.getUserName() + "\n" +
                "Email:  " + session.getUserEmail() + "\n" +
                "Role:    " + roleLabel + "\n\n" +
                "Contact the learning center administrator\n" +
                "to update your profile information."
            )
            .setPositiveButton("OK", null)
            .show();
    }

    // ── Change-password dialog ──────────────────────────────────────

    private void showSecurityDialog() {
        android.view.LayoutInflater inflater = android.view.LayoutInflater.from(this);
        android.view.View view = inflater.inflate(R.layout.activity_security, null);

        android.app.AlertDialog dialog = new android.app.AlertDialog.Builder(this)
            .setView(view).create();

        com.google.android.material.textfield.TextInputEditText etCurrent =
            view.findViewById(R.id.et_current_password);
        com.google.android.material.textfield.TextInputEditText etNew =
            view.findViewById(R.id.et_new_password);
        com.google.android.material.textfield.TextInputEditText etConfirm =
            view.findViewById(R.id.et_confirm_password);

        android.view.View btnBack = view.findViewById(R.id.btn_back);
        if (btnBack != null) btnBack.setOnClickListener(v -> dialog.dismiss());

        com.google.android.material.button.MaterialButton btnUpdate =
            view.findViewById(R.id.btn_update_password);
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

                // Show progress while request is in flight
                android.widget.ProgressBar pb = view.findViewById(R.id.progress_bar);
                if (pb != null) pb.setVisibility(android.view.View.VISIBLE);
                btnUpdate.setEnabled(false);

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
                            if (pb != null) pb.setVisibility(android.view.View.GONE);
                            btnUpdate.setEnabled(true);
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
                            if (pb != null) pb.setVisibility(android.view.View.GONE);
                            btnUpdate.setEnabled(true);
                            Toast.makeText(SettingsActivity.this,
                                "Network error.", Toast.LENGTH_SHORT).show();
                        }
                    });
            });
        }
        dialog.show();
    }

    // ── Logout confirmation ────────────────────────────────────────

    private void confirmLogout() {
        new android.app.AlertDialog.Builder(this)
            .setTitle("Log Out")
            .setMessage("Are you sure you want to log out?")
            .setPositiveButton("Log Out", (d, w) -> doLogout())
            .setNegativeButton("Cancel", null)
            .show();
    }
}
