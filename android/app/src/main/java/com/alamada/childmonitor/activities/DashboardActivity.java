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

        // ── Welcome: name + avatar initial ─────────────────────────
        String name = session.getUserName();
        if (name != null && !name.isEmpty()) {
            // Show full name with exclamation mark, e.g. "Juan Dela Cruz!"
            binding.tvWelcome.setText(name + "!");
            // Replace the default emoji with the first letter of the name
            binding.tvAvatar.setText(String.valueOf(name.charAt(0)).toUpperCase());
            binding.tvAvatar.setTextSize(34);
            binding.tvAvatar.setTextColor(
                getResources().getColor(android.R.color.white, null));
        }

        // ── Card clicks ─────────────────────────────────────────────
        binding.cardChildInfo.setOnClickListener(v ->
            startActivity(new Intent(this, ChildDetailActivity.class)));

        binding.cardSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        binding.cardNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));

        // ── Bottom nav ──────────────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> loadUnreadCount());   // already here — refresh

        binding.tabChild.setOnClickListener(v ->
            startActivity(new Intent(this, ChildDetailActivity.class)));

        binding.tabSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        loadUnreadCount();
    }

    @Override
    protected void onResume() {
        super.onResume();
        loadUnreadCount();
    }

    // ── Unread notification badge ───────────────────────────────────

    private void loadUnreadCount() {
        ApiClient.getInstance(session.getBearerToken())
            .getUnreadCount()
            .enqueue(new Callback<UnreadCountResponse>() {
                @Override
                public void onResponse(Call<UnreadCountResponse> call,
                                       Response<UnreadCountResponse> response) {
                    if (response.isSuccessful() && response.body() != null) {
                        updateBadge(response.body().unreadCount);
                    } else if (response.code() == 401) {
                        handleUnauthorised();
                    }
                }
                @Override
                public void onFailure(Call<UnreadCountResponse> call, Throwable t) {}
            });
    }

    private void updateBadge(int count) {
        if (binding.tvNotifBadge != null) {
            binding.tvNotifBadge.setVisibility(count > 0 ? View.VISIBLE : View.GONE);
            binding.tvNotifBadge.setText(count > 9 ? "9+" : String.valueOf(count));
        }
    }
}
