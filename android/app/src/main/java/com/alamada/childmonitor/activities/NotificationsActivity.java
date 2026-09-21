package com.alamada.childmonitor.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.NotificationAdapter;
import com.alamada.childmonitor.databinding.ActivityNotificationsBinding;
import com.alamada.childmonitor.models.MessageResponse;
import com.alamada.childmonitor.models.Notification;
import com.alamada.childmonitor.models.NotificationListResponse;
import com.alamada.childmonitor.network.ApiClient;
import com.alamada.childmonitor.utils.SessionManager;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class NotificationsActivity extends AppCompatActivity {

    private ActivityNotificationsBinding binding;
    private SessionManager session;
    private NotificationAdapter adapter;
    private final List<Notification> notifications = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityNotificationsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        session = new SessionManager(this);

        // Back button
        binding.btnBack.setOnClickListener(v -> finish());

        // Mark all read
        binding.btnMarkAll.setOnClickListener(v -> markAllRead());

        // RecyclerView
        adapter = new NotificationAdapter(notifications, notification -> {
            if (!notification.isRead) {
                ApiClient.getInstance(session.getBearerToken())
                    .markNotificationRead(notification.id)
                    .enqueue(new Callback<MessageResponse>() {
                        @Override
                        public void onResponse(Call<MessageResponse> call,
                                               Response<MessageResponse> response) {
                            notification.isRead = true;
                            adapter.notifyDataSetChanged();
                        }
                        @Override public void onFailure(Call<MessageResponse> call, Throwable t) {}
                    });
            }
        });

        binding.rvNotifications.setLayoutManager(new LinearLayoutManager(this));
        binding.rvNotifications.setAdapter(adapter);

        binding.swipeRefresh.setColorSchemeResources(R.color.primary);
        binding.swipeRefresh.setOnRefreshListener(this::loadNotifications);

        loadNotifications();
    }

    private void loadNotifications() {
        binding.progressBar.setVisibility(View.VISIBLE);

        ApiClient.getInstance(session.getBearerToken())
            .getNotifications()
            .enqueue(new Callback<NotificationListResponse>() {
                @Override
                public void onResponse(Call<NotificationListResponse> call,
                                       Response<NotificationListResponse> response) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);

                    if (response.isSuccessful() && response.body() != null) {
                        notifications.clear();
                        if (response.body().data != null)
                            notifications.addAll(response.body().data);
                        adapter.notifyDataSetChanged();

                        boolean empty = notifications.isEmpty();
                        binding.tvEmpty.setVisibility(empty ? View.VISIBLE : View.GONE);
                        binding.rvNotifications.setVisibility(empty ? View.GONE : View.VISIBLE);
                    }
                }

                @Override
                public void onFailure(Call<NotificationListResponse> call, Throwable t) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                    Toast.makeText(NotificationsActivity.this,
                        "Could not load notifications.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    private void markAllRead() {
        ApiClient.getInstance(session.getBearerToken())
            .markAllRead()
            .enqueue(new Callback<MessageResponse>() {
                @Override
                public void onResponse(Call<MessageResponse> call,
                                       Response<MessageResponse> response) {
                    if (response.isSuccessful()) {
                        for (Notification n : notifications) n.isRead = true;
                        adapter.notifyDataSetChanged();
                        Toast.makeText(NotificationsActivity.this,
                            "All marked as read.", Toast.LENGTH_SHORT).show();
                    }
                }
                @Override public void onFailure(Call<MessageResponse> call, Throwable t) {}
            });
    }
}
