package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.alamada.childmonitor.databinding.ActivityDashboardBinding;
import com.alamada.childmonitor.models.UnreadCountResponse;
import com.alamada.childmonitor.network.ApiClient;
import com.alamada.childmonitor.utils.SessionManager;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class DashboardActivity extends AppCompatActivity {

    private ActivityDashboardBinding binding;
    private SessionManager session;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityDashboardBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        // Hide default action bar — we have a custom header
        if (getSupportActionBar() != null) getSupportActionBar().hide();

        session = new SessionManager(this);

        // Set welcome name
        String name = session.getUserName();
        if (name != null && !name.isEmpty()) {
            binding.tvWelcome.setText(name + "!");
        }

        // Set avatar initial
        if (name != null && !name.isEmpty()) {
            binding.tvAvatar.setText(String.valueOf(name.charAt(0)).toUpperCase());
            binding.tvAvatar.setTextSize(28);
            binding.tvAvatar.setTextColor(
                getResources().getColor(android.R.color.white, null));
        }

        // ── Quick Access card clicks ────────────────────────
        binding.cardChildInfo.setOnClickListener(v -> {
            startActivity(new Intent(this, ChildDetailActivity.class));
        });

        binding.cardSettings.setOnClickListener(v -> {
            startActivity(new Intent(this, SettingsActivity.class));
        });

        binding.cardNotifications.setOnClickListener(v -> {
            startActivity(new Intent(this, NotificationsActivity.class));
        });

        // ── Bottom nav clicks ──────────────────────────────
        binding.tabHome.setOnClickListener(v -> {
            // Already on home — refresh
            setActiveTab("home");
            loadUnreadCount();
        });

        binding.tabChild.setOnClickListener(v -> {
            setActiveTab("child");
            startActivity(new Intent(this, ChildDetailActivity.class));
        });

        binding.tabSettings.setOnClickListener(v -> {
            setActiveTab("settings");
            startActivity(new Intent(this, SettingsActivity.class));
        });

        // Load unread notification count
        loadUnreadCount();
    }

    @Override
    protected void onResume() {
        super.onResume();
        setActiveTab("home");
        loadUnreadCount();
    }

    // ── Load unread notifications badge ──────────────────

    private void loadUnreadCount() {
        ApiClient.getInstance(session.getBearerToken())
            .getUnreadCount()
            .enqueue(new Callback<UnreadCountResponse>() {
                @Override
                public void onResponse(Call<UnreadCountResponse> call,
                                       Response<UnreadCountResponse> response) {
                    if (response.isSuccessful() && response.body() != null) {
                        int count = response.body().unreadCount;
                        if (count > 0) {
                            binding.tvNotifBadge.setVisibility(View.VISIBLE);
                            binding.tvNotifBadge.setText(count > 9 ? "9+" : String.valueOf(count));
                        } else {
                            binding.tvNotifBadge.setVisibility(View.GONE);
                        }
                    }
                }
                @Override
                public void onFailure(Call<UnreadCountResponse> call, Throwable t) { }
            });
    }

    // ── Active tab highlight ──────────────────────────────

    private void setActiveTab(String tab) {
        int activeColor   = getResources().getColor(R.color.status_present, null); // green
        int inactiveColor = getResources().getColor(android.R.color.darker_gray, null);

        binding.tabHomeLabel.setTextColor(
            "home".equals(tab) ? activeColor : inactiveColor);
    }

    // ── Session expired ───────────────────────────────────

    private void sessionExpired() {
        session.clearSession();
        Intent i = new Intent(this, LoginActivity.class);
        i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        startActivity(i);
    }
}
