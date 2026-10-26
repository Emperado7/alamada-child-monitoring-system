package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.EnrollmentAdapter;
import com.alamada.childmonitor.databinding.ActivityEnrollmentBinding;
import com.alamada.childmonitor.models.Enrollment;
import com.alamada.childmonitor.models.EnrollmentListResponse;
import com.alamada.childmonitor.network.ApiClient;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class EnrollmentActivity extends BaseActivity {

    private ActivityEnrollmentBinding binding;
    private EnrollmentAdapter adapter;
    private final List<Enrollment> enrollments = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityEnrollmentBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        binding.btnBack.setOnClickListener(v -> finish());

        adapter = new EnrollmentAdapter(enrollments);
        binding.rvEnrollments.setLayoutManager(new LinearLayoutManager(this));
        binding.rvEnrollments.setAdapter(adapter);

        binding.swipeRefresh.setColorSchemeResources(R.color.primary);
        binding.swipeRefresh.setOnRefreshListener(this::loadEnrollments);

        // ── Bottom nav ─────────────────────────────────────────────
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
        binding.tabSettings.setOnClickListener(v -> {
            startActivity(new Intent(this, SettingsActivity.class));
            finish();
        });

        loadEnrollments();
    }

    private void loadEnrollments() {
        binding.progressBar.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getEnrollments()
            .enqueue(new Callback<EnrollmentListResponse>() {
                @Override
                public void onResponse(Call<EnrollmentListResponse> call,
                                       Response<EnrollmentListResponse> response) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.isSuccessful() && response.body() != null) {
                        enrollments.clear();
                        if (response.body().data != null)
                            enrollments.addAll(response.body().data);
                        adapter.notifyDataSetChanged();
                        boolean empty = enrollments.isEmpty();
                        binding.tvEmpty.setVisibility(empty ? View.VISIBLE : View.GONE);
                        binding.rvEnrollments.setVisibility(empty ? View.GONE : View.VISIBLE);
                    }
                }
                @Override
                public void onFailure(Call<EnrollmentListResponse> call, Throwable t) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                    Toast.makeText(EnrollmentActivity.this,
                        "Could not load enrollment records.", Toast.LENGTH_SHORT).show();
                }
            });
    }
}
