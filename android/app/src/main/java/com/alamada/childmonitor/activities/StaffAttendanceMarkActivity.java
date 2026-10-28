package com.alamada.childmonitor.activities;

import android.app.DatePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.StaffAttendanceAdapter;
import com.alamada.childmonitor.databinding.ActivityStaffAttendanceMarkBinding;
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
 * Staff / Admin "Mark Attendance" screen — lets staff pick a date,
 * see all children, and mark each as present/absent/excused.
 * Saves via the /api/attendance/bulk endpoint.
 */
public class StaffAttendanceMarkActivity extends BaseActivity {

    private ActivityStaffAttendanceMarkBinding binding;
    private StaffAttendanceAdapter adapter;
    private final List<Child> children = new ArrayList<>();
    private String selectedDate;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityStaffAttendanceMarkBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        // Default to today
        selectedDate = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
            .format(Calendar.getInstance().getTime());
        updateDateLabel();

        adapter = new StaffAttendanceAdapter(children);
        binding.rvAttendance.setLayoutManager(new LinearLayoutManager(this));
        binding.rvAttendance.setAdapter(adapter);

        binding.btnBack.setOnClickListener(v -> finish());
        binding.btnPickDate.setOnClickListener(v -> showDatePicker());
        binding.btnSave.setOnClickListener(v -> saveAttendance());

        binding.swipeRefresh.setColorSchemeResources(R.color.primary);
        binding.swipeRefresh.setOnRefreshListener(this::loadChildren);

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

    private void showDatePicker() {
        Calendar cal = Calendar.getInstance();
        new DatePickerDialog(this, (view, year, month, day) -> {
            Calendar picked = Calendar.getInstance();
            picked.set(year, month, day);
            selectedDate = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                .format(picked.getTime());
            updateDateLabel();
        }, cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH))
            .show();
    }

    private void updateDateLabel() {
        try {
            SimpleDateFormat inFmt  = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
            SimpleDateFormat outFmt = new SimpleDateFormat("EEEE, MMM dd, yyyy", Locale.getDefault());
            binding.tvDate.setText(outFmt.format(inFmt.parse(selectedDate)));
        } catch (Exception e) {
            binding.tvDate.setText(selectedDate);
        }
    }

    private void loadChildren() {
        binding.progressBar.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getAllChildren()
            .enqueue(new Callback<ChildListResponse>() {
                @Override
                public void onResponse(Call<ChildListResponse> call,
                                       Response<ChildListResponse> response) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.isSuccessful() && response.body() != null
                            && response.body().data != null) {
                        children.clear();
                        children.addAll(response.body().data);
                        adapter = new StaffAttendanceAdapter(children);
                        binding.rvAttendance.setAdapter(adapter);
                        boolean empty = children.isEmpty();
                        binding.tvEmpty.setVisibility(empty ? View.VISIBLE : View.GONE);
                        binding.rvAttendance.setVisibility(empty ? View.GONE : View.VISIBLE);
                    }
                }
                @Override
                public void onFailure(Call<ChildListResponse> call, Throwable t) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                    Toast.makeText(StaffAttendanceMarkActivity.this,
                        "Could not load children.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    private void saveAttendance() {
        if (children.isEmpty()) {
            Toast.makeText(this, "No children to mark.", Toast.LENGTH_SHORT).show();
            return;
        }

        String[] statuses = adapter.getSelectedStatuses();
        List<Map<String, Object>> records = new ArrayList<>();
        for (int i = 0; i < children.size(); i++) {
            Map<String, Object> rec = new HashMap<>();
            rec.put("child_id", children.get(i).id);
            rec.put("status", statuses[i] != null ? statuses[i] : "present");
            records.add(rec);
        }

        Map<String, Object> body = new HashMap<>();
        body.put("date", selectedDate);
        body.put("records", records);

        binding.btnSave.setEnabled(false);
        binding.btnSave.setText("Saving…");

        ApiClient.getInstance(session.getBearerToken())
            .recordBulkAttendance(body)
            .enqueue(new Callback<MessageResponse>() {
                @Override
                public void onResponse(Call<MessageResponse> call,
                                       Response<MessageResponse> response) {
                    binding.btnSave.setEnabled(true);
                    binding.btnSave.setText("Save Attendance");
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.isSuccessful()) {
                        Toast.makeText(StaffAttendanceMarkActivity.this,
                            "Attendance saved successfully.", Toast.LENGTH_SHORT).show();
                    } else {
                        Toast.makeText(StaffAttendanceMarkActivity.this,
                            "Failed to save attendance.", Toast.LENGTH_SHORT).show();
                    }
                }
                @Override
                public void onFailure(Call<MessageResponse> call, Throwable t) {
                    binding.btnSave.setEnabled(true);
                    binding.btnSave.setText("Save Attendance");
                    Toast.makeText(StaffAttendanceMarkActivity.this,
                        "Network error.", Toast.LENGTH_SHORT).show();
                }
            });
    }
}
