package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;

import com.alamada.childmonitor.databinding.ActivityDashboardBinding;
import com.alamada.childmonitor.models.UnreadCountResponse;
import com.alamada.childmonitor.network.ApiClient;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class DashboardActivity extends BaseActivity {

    private ActivityDashboardBinding binding;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityDashboardBinding.inflate(getLayoutInflater());
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
        binding.cardChildInfo.setOnClickListener(v ->
            startActivity(new Intent(this, ChildDetailActivity.class)));

        binding.cardEnrollment.setOnClickListener(v ->
            startActivity(new Intent(this, EnrollmentActivity.class)));

        binding.cardNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));

        binding.cardSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        // ── Bottom nav ─────────────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> loadUnreadCount());

        binding.tabChild.setOnClickListener(v ->
            startActivity(new Intent(this, ChildDetailActivity.class)));

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
                        int count = response.body().unreadCount;
                        updateNotifBadge(count);
                    } else if (response.code() == 401) {
                        handleUnauthorised();
                    }
                }
                @Override public void onFailure(Call<UnreadCountResponse> call, Throwable t) {}
            });
    }

    private void updateNotifBadge(int count) {
        // Card badge
        if (binding.tvNotifBadge != null) {
            binding.tvNotifBadge.setVisibility(count > 0 ? View.VISIBLE : View.GONE);
            binding.tvNotifBadge.setText(count > 9 ? "9+" : String.valueOf(count));
        }
        // Bottom-nav badge
        if (binding.tvNavNotifBadge != null) {
            binding.tvNavNotifBadge.setVisibility(count > 0 ? View.VISIBLE : View.GONE);
            binding.tvNavNotifBadge.setText(count > 9 ? "9+" : String.valueOf(count));
        }
    }
}
