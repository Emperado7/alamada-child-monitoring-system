package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.databinding.ActivityStaffDashboardBinding;
import com.alamada.childmonitor.models.UnreadCountResponse;
import com.alamada.childmonitor.network.ApiClient;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Staff / Admin dashboard — the landing screen after login for staff and admin roles.
 * Provides quick access to Children, Mark Attendance, Activity Log,
 * Notifications, and Settings.
 */
public class StaffDashboardActivity extends BaseActivity {

    private ActivityStaffDashboardBinding binding;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityStaffDashboardBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        // Welcome name + avatar initial
        String name = session.getUserName();
        if (name != null && !name.isEmpty()) {
            binding.tvWelcome.setText(name + "!");
            binding.tvAvatar.setText(String.valueOf(name.charAt(0)).toUpperCase());
            binding.tvAvatar.setTextSize(28);
            binding.tvAvatar.setTextColor(
                getResources().getColor(android.R.color.white, null));
        }

        // ── Quick-access card clicks ───────────────────────────────
        binding.cardChildren.setOnClickListener(v ->
            startActivity(new Intent(this, StaffChildListActivity.class)));

        binding.cardMarkAttendance.setOnClickListener(v ->
            startActivity(new Intent(this, StaffAttendanceMarkActivity.class)));

        binding.cardActivityLog.setOnClickListener(v ->
            startActivity(new Intent(this, StaffActivityLogActivity.class)));

        binding.cardReports.setOnClickListener(v ->
            startActivity(new Intent(this, ReportsActivity.class)));

        binding.cardNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));

        binding.cardSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        // ── Bottom nav ─────────────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> loadUnreadCount());
        binding.tabChildren.setOnClickListener(v ->
            startActivity(new Intent(this, StaffChildListActivity.class)));
        binding.tabNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));
        binding.tabSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        loadUnreadCount();
    }

    @Override
    protected void onResume() {
        super.onResume();
        loadUnreadCount();
    }

    // ── Unread notification badge ──────────────────────────────────

    private void loadUnreadCount() {
        ApiClient.getInstance(session.getBearerToken())
            .getUnreadCount()
            .enqueue(new Callback<UnreadCountResponse>() {
                @Override
                public void onResponse(Call<UnreadCountResponse> call,
                                       Response<UnreadCountResponse> response) {
                    if (response.isSuccessful() && response.body() != null) {
                        updateNotifBadge(response.body().unreadCount);
                    } else if (response.code() == 401) {
                        handleUnauthorised();
                    }
                }
                @Override public void onFailure(Call<UnreadCountResponse> call, Throwable t) {}
            });
    }

    private void updateNotifBadge(int count) {
        if (binding.tvNotifBadge != null) {
            binding.tvNotifBadge.setVisibility(count > 0 ? View.VISIBLE : View.GONE);
            binding.tvNotifBadge.setText(count > 9 ? "9+" : String.valueOf(count));
        }
    }
}
