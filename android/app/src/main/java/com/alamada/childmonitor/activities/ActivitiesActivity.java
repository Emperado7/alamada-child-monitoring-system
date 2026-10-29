package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.ActivityAdapter;
import com.alamada.childmonitor.databinding.ActivityActivitiesBinding;
import com.alamada.childmonitor.models.ActivityListResponse;
import com.alamada.childmonitor.models.ActivityRecord;
import com.alamada.childmonitor.network.ApiClient;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Daily Activities screen — full-screen list of activities for a child.
 * Launched from ChildDetailActivity when the parent taps "Daily Activities".
 */
public class ActivitiesActivity extends BaseActivity {

    private ActivityActivitiesBinding binding;
    private ActivityAdapter adapter;
    private final List<ActivityRecord> activities = new ArrayList<>();
    private int childId = -1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityActivitiesBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        childId = getIntent().getIntExtra("child_id", -1);

        binding.btnBack.setOnClickListener(v -> finish());

        adapter = new ActivityAdapter(activities);
        binding.rvActivities.setLayoutManager(new LinearLayoutManager(this));
        binding.rvActivities.setAdapter(adapter);

        binding.swipeRefresh.setColorSchemeResources(R.color.primary);
        binding.swipeRefresh.setOnRefreshListener(this::loadActivities);

        // Bottom nav
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this, DashboardActivity.class));
            finish();
        });
        binding.tabChild.setOnClickListener(v -> {
            Intent i = new Intent(this, ChildDetailActivity.class);
            i.putExtra("child_id", childId);
            startActivity(i);
            finish();
        });
        binding.tabSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        loadActivities();
    }

    private void loadActivities() {
        if (childId == -1) return;
        binding.progressBar.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getActivities(childId)
            .enqueue(new Callback<ActivityListResponse>() {
                @Override
                public void onResponse(Call<ActivityListResponse> call,
                                       Response<ActivityListResponse> response) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.isSuccessful() && response.body() != null) {
                        activities.clear();
                        if (response.body().data != null)
                            activities.addAll(response.body().data);
                        adapter.notifyDataSetChanged();
                        boolean empty = activities.isEmpty();
                        binding.layoutEmpty.setVisibility(empty ? View.VISIBLE : View.GONE);
                        binding.rvActivities.setVisibility(empty ? View.GONE : View.VISIBLE);
                    }
                }
                @Override
                public void onFailure(Call<ActivityListResponse> call, Throwable t) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                }
            });
    }
}
