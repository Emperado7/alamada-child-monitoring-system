package com.alamada.childmonitor.activities;

import android.os.Bundle;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.AttendanceAdapter;
import com.alamada.childmonitor.databinding.ActivityAttendanceBinding;
import com.alamada.childmonitor.models.AttendanceRecord;
import com.alamada.childmonitor.models.AttendanceResponse;
import com.alamada.childmonitor.models.AttendanceSummaryResponse;
import com.alamada.childmonitor.models.Child;
import com.alamada.childmonitor.models.ChildListResponse;
import com.alamada.childmonitor.network.ApiClient;
import com.alamada.childmonitor.utils.SessionManager;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;
import java.util.Locale;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class AttendanceActivity extends AppCompatActivity {

    private ActivityAttendanceBinding binding;
    private SessionManager session;
    private AttendanceAdapter adapter;
    private final List<AttendanceRecord> records  = new ArrayList<>();
    private final List<Child>            children = new ArrayList<>();
    private int    selectedChildId = -1;
    private String selectedMonth;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityAttendanceBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle("Attendance");
        }

        session = new SessionManager(this);
        selectedMonth = new SimpleDateFormat("yyyy-MM", Locale.getDefault())
            .format(Calendar.getInstance().getTime());

        updateMonthLabel();

        adapter = new AttendanceAdapter(records);
        binding.rvAttendance.setLayoutManager(new LinearLayoutManager(this));
        binding.rvAttendance.setAdapter(adapter);

        binding.btnPrevMonth.setOnClickListener(v -> shiftMonth(-1));
        binding.btnNextMonth.setOnClickListener(v -> shiftMonth(+1));

        binding.swipeRefresh.setColorSchemeResources(R.color.primary);
        binding.swipeRefresh.setOnRefreshListener(this::loadAttendance);

        loadChildren();
    }

    private void loadChildren() {
        ApiClient.getInstance(session.getBearerToken())
            .getMyChildren()
            .enqueue(new Callback<ChildListResponse>() {
                @Override
                public void onResponse(Call<ChildListResponse> call,
                                       Response<ChildListResponse> response) {
                    if (response.isSuccessful() && response.body() != null
                            && response.body().data != null) {
                        children.clear();
                        children.addAll(response.body().data);
                        setupSpinner();
                    }
                }
                @Override
                public void onFailure(Call<ChildListResponse> call, Throwable t) {
                    Toast.makeText(AttendanceActivity.this,
                        "Could not load children.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    private void setupSpinner() {
        List<String> names = new ArrayList<>();
        names.add("All Children");
        for (Child c : children) names.add(c.getDisplayName());

        ArrayAdapter<String> spinnerAdapter = new ArrayAdapter<>(
            this, android.R.layout.simple_spinner_item, names);
        spinnerAdapter.setDropDownViewResource(
            android.R.layout.simple_spinner_dropdown_item);
        binding.spinnerChild.setAdapter(spinnerAdapter);

        binding.spinnerChild.setOnItemSelectedListener(
            new AdapterView.OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> p, View v, int pos, long id) {
                    selectedChildId = (pos == 0) ? -1 : children.get(pos - 1).id;
                    loadAttendance();
                }
                @Override public void onNothingSelected(AdapterView<?> p) { }
            });
    }

    private void loadAttendance() {
        if (selectedChildId == -1) {
            records.clear();
            adapter.notifyDataSetChanged();
            binding.cardSummary.setVisibility(View.GONE);
            return;
        }

        binding.progressBar.setVisibility(View.VISIBLE);

        ApiClient.getInstance(session.getBearerToken())
            .getAttendance(selectedChildId, selectedMonth)
            .enqueue(new Callback<AttendanceResponse>() {
                @Override
                public void onResponse(Call<AttendanceResponse> call,
                                       Response<AttendanceResponse> response) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                    if (response.isSuccessful() && response.body() != null) {
                        records.clear();
                        if (response.body().data != null)
                            records.addAll(response.body().data);
                        adapter.notifyDataSetChanged();
                        binding.tvEmpty.setVisibility(records.isEmpty() ? View.VISIBLE : View.GONE);
                        binding.rvAttendance.setVisibility(records.isEmpty() ? View.GONE : View.VISIBLE);
                    }
                }
                @Override
                public void onFailure(Call<AttendanceResponse> call, Throwable t) {
                    binding.progressBar.setVisibility(View.GONE);
                    binding.swipeRefresh.setRefreshing(false);
                }
            });

        // Load summary stats
        ApiClient.getInstance(session.getBearerToken())
            .getAttendanceSummary(selectedMonth)
            .enqueue(new Callback<AttendanceSummaryResponse>() {
                @Override
                public void onResponse(Call<AttendanceSummaryResponse> call,
                                       Response<AttendanceSummaryResponse> response) {
                    if (response.isSuccessful() && response.body() != null
                            && response.body().summary != null) {
                        for (AttendanceSummaryResponse.Summary s : response.body().summary) {
                            if (s.childId == selectedChildId) {
                                binding.cardSummary.setVisibility(View.VISIBLE);
                                binding.tvPresent.setText(String.valueOf(s.present));
                                binding.tvAbsent.setText(String.valueOf(s.absent));
                                binding.tvLate.setText(String.valueOf(s.late));
                                binding.tvRate.setText(s.attendanceRate);
                                return;
                            }
                        }
                    }
                    binding.cardSummary.setVisibility(View.GONE);
                }
                @Override public void onFailure(Call<AttendanceSummaryResponse> call, Throwable t) { }
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
            loadAttendance();
        } catch (Exception ignored) { }
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

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        if (item.getItemId() == android.R.id.home) { finish(); return true; }
        return super.onOptionsItemSelected(item);
    }
}
