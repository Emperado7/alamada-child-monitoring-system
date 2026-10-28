package com.alamada.childmonitor.activities;

import android.app.AlertDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Spinner;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.StaffActivityAdapter;
import com.alamada.childmonitor.databinding.ActivityStaffActivityLogBinding;
import com.alamada.childmonitor.models.ActivityListResponse;
import com.alamada.childmonitor.models.ActivityRecord;
import com.alamada.childmonitor.models.Child;
import com.alamada.childmonitor.models.ChildListResponse;
import com.alamada.childmonitor.models.MessageResponse;
import com.alamada.childmonitor.network.ApiClient;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Staff / Admin Activity Log screen — view and record activities per child per month.
 * Uses the /api/activities endpoint (apiResource — full CRUD for staff/admin).
 */
public class StaffActivityLogActivity extends BaseActivity {

    private ActivityStaffActivityLogBinding binding;
    private StaffActivityAdapter adapter;
    private final List<ActivityRecord> activities = new ArrayList<>();
    private final List<Child> children = new ArrayList<>();
    private int selectedChildId = -1;
    private String selectedMonth;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityStaffActivityLogBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        selectedMonth = new SimpleDateFormat("yyyy-MM", Locale.getDefault())
            .format(Calendar.getInstance().getTime());
        updateMonthLabel();

        adapter = new StaffActivityAdapter(activities);
        binding.rvActivities.setLayoutManager(new LinearLayoutManager(this));
        binding.rvActivities.setAdapter(adapter);

        binding.btnBack.setOnClickListener(v -> finish());
        binding.btnPrevMonth.setOnClickListener(v -> shiftMonth(-1));
        binding.btnNextMonth.setOnClickListener(v -> shiftMonth(+1));
        binding.btnAdd.setOnClickListener(v -> showAddActivityDialog());

        binding.swipeRefresh.setColorSchemeResources(R.color.primary);
        binding.swipeRefresh.setOnRefreshListener(this::loadActivities);

        // ── Bottom nav ─────────────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this, StaffDashboardActivity.class));
            finish();
        });
        binding.tabNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));
        binding.tabSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        loadChildren();
    }

    private void loadChildren() {
        ApiClient.getInstance(session.getBearerToken())
            .getAllChildren()
            .enqueue(new Callback<ChildListResponse>() {
                @Override
                public void onResponse(Call<ChildListResponse> call,
                                       Response<ChildListResponse> response) {
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.isSuccessful() && response.body() != null
                            && response.body().data != null) {
                        children.clear();
                        children.addAll(response.body().data);
                        setupSpinner();
                    }
                }
                @Override
                public void onFailure(Call<ChildListResponse> call, Throwable t) {
                    Toast.makeText(StaffActivityLogActivity.this,
                        "Could not load children.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    private void setupSpinner() {
        List<String> names = new ArrayList<>();
        if (children.size() == 1) {
            selectedChildId = children.get(0).id;
            names.add(children.get(0).getDisplayName());
        } else {
            names.add("Select a child…");
            for (Child c : children) names.add(c.getDisplayName());
        }

        ArrayAdapter<String> spinnerAdapter = new ArrayAdapter<>(
            this, android.R.layout.simple_spinner_item, names);
        spinnerAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        binding.spinnerChild.setAdapter(spinnerAdapter);

        binding.spinnerChild.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> p, View v, int pos, long id) {
                if (children.size() == 1) {
                    selectedChildId = children.get(0).id;
                } else {
                    selectedChildId = (pos == 0) ? -1 : children.get(pos - 1).id;
                }
                loadActivities();
            }
            @Override public void onNothingSelected(AdapterView<?> p) {}
        });

        if (children.size() == 1) loadActivities();
    }

    private void loadActivities() {
        if (selectedChildId == -1) {
            activities.clear();
            adapter.notifyDataSetChanged();
            binding.tvEmpty.setVisibility(View.VISIBLE);
            binding.rvActivities.setVisibility(View.GONE);
            return;
        }

        binding.progressBar.setVisibility(View.VISIBLE);

        ApiClient.getInstance(session.getBearerToken())
            .getAllActivities(selectedChildId, selectedMonth)
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
                        binding.tvEmpty.setVisibility(empty ? View.VISIBLE : View.GONE);
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

    private void shiftMonth(int delta) {
        try {
            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM", Locale.getDefault());
            Calendar cal = Calendar.getInstance();
            cal.setTime(sdf.parse(selectedMonth));
            cal.add(Calendar.MONTH, delta);
            selectedMonth = sdf.format(cal.getTime());
            updateMonthLabel();
            loadActivities();
        } catch (Exception ignored) {}
    }

    private void updateMonthLabel() {
        try {
            SimpleDateFormat inFmt  = new SimpleDateFormat("yyyy-MM", Locale.getDefault());
            SimpleDateFormat outFmt = new SimpleDateFormat("MMMM yyyy", Locale.getDefault());
            binding.tvMonth.setText(outFmt.format(inFmt.parse(selectedMonth)));
        } catch (Exception e) {
            binding.tvMonth.setText(selectedMonth);
        }
    }

    // ── Add Activity Dialog ────────────────────────────────────────

    private void showAddActivityDialog() {
        if (selectedChildId == -1) {
            Toast.makeText(this, "Please select a child first.", Toast.LENGTH_SHORT).show();
            return;
        }

        String today = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
            .format(Calendar.getInstance().getTime());

        String[] types      = {"Learning", "Play", "Therapy", "Feeding", "Rest", "Other"};
        String[] statuses   = {"Completed", "In Progress", "Not Started"};
        String[] statusKeys  = {"completed", "in_progress", "not_started"};

        final int[] typeIdx   = {0};
        final int[] statusIdx  = {0};

        // Step 1: Select activity type
        new AlertDialog.Builder(this)
            .setTitle("Log Activity")
            .setMessage("Child: " + getChildName(selectedChildId) + "\nDate: " + today + "\n\nSelect activity type:")
            .setSingleChoiceItems(types, 0, (d, which) -> typeIdx[0] = which)
            .setPositiveButton("Next", (d, which) -> {
                // Step 2: Select completion status
                new AlertDialog.Builder(this)
                    .setTitle("Completion Status")
                    .setMessage("Activity: " + types[typeIdx[0]])
                    .setSingleChoiceItems(statuses, 0, (dd, w) -> statusIdx[0] = w)
                    .setPositiveButton("Save", (dd, w) -> {
                        saveNewActivity(
                            types[typeIdx[0]].toLowerCase().replace(" ", "_"),
                            statusKeys[statusIdx[0]],
                            today
                        );
                    })
                    .setNegativeButton("Cancel", null)
                    .show();
            })
            .setNegativeButton("Cancel", null)
            .show();
    }

    private void saveNewActivity(String type, String status, String date) {
        Map<String, Object> body = new HashMap<>();
        body.put("child_id", selectedChildId);
        body.put("activity_date", date);
        body.put("activity_type", type);
        body.put("completion_status", status);

        ApiClient.getInstance(session.getBearerToken())
            .logActivity(body)
            .enqueue(new Callback<MessageResponse>() {
                @Override
                public void onResponse(Call<MessageResponse> call,
                                       Response<MessageResponse> response) {
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.isSuccessful()) {
                        Toast.makeText(StaffActivityLogActivity.this,
                            "Activity logged.", Toast.LENGTH_SHORT).show();
                        loadActivities();
                    } else {
                        Toast.makeText(StaffActivityLogActivity.this,
                            "Failed to log activity.", Toast.LENGTH_SHORT).show();
                    }
                }
                @Override
                public void onFailure(Call<MessageResponse> call, Throwable t) {
                    Toast.makeText(StaffActivityLogActivity.this,
                        "Network error.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    private String getChildName(int childId) {
        for (Child c : children) {
            if (c.id == childId) return c.getDisplayName();
        }
        return "Unknown";
    }
}
