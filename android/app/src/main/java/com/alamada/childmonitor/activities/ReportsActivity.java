package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.ReportAttendanceAdapter;
import com.alamada.childmonitor.databinding.ActivityReportsBinding;
import com.alamada.childmonitor.models.AttendanceReportResponse;
import com.alamada.childmonitor.models.EnrollmentReportResponse;
import com.alamada.childmonitor.network.ApiClient;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;
import java.util.Locale;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Reports screen (admin-only) — toggle between Enrollment and Attendance reports.
 *
 * Enrollment: shows total enrolled, year-over-year comparison, breakdowns by
 * gender, age group, and diagnosis.
 *
 * Attendance: shows avg attendance for a selected month, plus a per-child
 * breakdown list.
 */
public class ReportsActivity extends BaseActivity {

    private ActivityReportsBinding binding;
    private ReportAttendanceAdapter attendanceAdapter;
    private final List<AttendanceReportResponse.ChildAttendance> attendanceItems = new ArrayList<>();
    private String selectedMonth;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityReportsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        binding.btnBack.setOnClickListener(v -> finish());

        // ── Toggle group: Enrollment vs Attendance ────────────────
        binding.toggleReports.addOnButtonCheckedListener((group, checkedId, isChecked) -> {
            if (!isChecked) return;
            if (checkedId == R.id.btn_tab_enrollment) {
                binding.scrollEnrollment.setVisibility(View.VISIBLE);
                binding.layoutAttendanceReport.setVisibility(View.GONE);
                binding.progressBarEnrollment.setVisibility(View.GONE);
                loadEnrollmentReport();
            } else if (checkedId == R.id.btn_tab_attendance) {
                binding.scrollEnrollment.setVisibility(View.GONE);
                binding.layoutAttendanceReport.setVisibility(View.VISIBLE);
                binding.progressBarEnrollment.setVisibility(View.GONE);
                loadAttendanceReport();
            }
        });

        // ── Attendance month navigator ────────────────────────────
        selectedMonth = new SimpleDateFormat("yyyy-MM", Locale.getDefault())
            .format(Calendar.getInstance().getTime());
        updateReportMonthLabel();

        binding.btnPrevMonth.setOnClickListener(v -> shiftReportMonth(-1));
        binding.btnNextMonth.setOnClickListener(v -> shiftReportMonth(+1));

        binding.swipeRefreshAttendance.setColorSchemeResources(R.color.primary);
        binding.swipeRefreshAttendance.setOnRefreshListener(this::loadAttendanceReport);

        attendanceAdapter = new ReportAttendanceAdapter(attendanceItems);
        binding.rvAttendanceReport.setLayoutManager(new LinearLayoutManager(this));
        binding.rvAttendanceReport.setAdapter(attendanceAdapter);

        // ── Bottom nav ─────────────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this, StaffDashboardActivity.class));
            finish();
        });
        binding.tabNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));
        binding.tabSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        // Load enrollment report by default
        loadEnrollmentReport();
    }

    // ── Enrollment Report ──────────────────────────────────────────

    private void loadEnrollmentReport() {
        binding.progressBarEnrollment.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getEnrollmentReport(null, null, null, null)
            .enqueue(new Callback<EnrollmentReportResponse>() {
                @Override
                public void onResponse(Call<EnrollmentReportResponse> call,
                                       Response<EnrollmentReportResponse> response) {
                    binding.progressBarEnrollment.setVisibility(View.GONE);
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.code() == 403) {
                        Toast.makeText(ReportsActivity.this,
                            "Reports are available to administrators only.",
                            Toast.LENGTH_LONG).show();
                        return;
                    }
                    if (response.isSuccessful() && response.body() != null) {
                        populateEnrollment(response.body());
                    }
                }
                @Override
                public void onFailure(Call<EnrollmentReportResponse> call, Throwable t) {
                    binding.progressBarEnrollment.setVisibility(View.GONE);
                    Toast.makeText(ReportsActivity.this,
                        "Could not load enrollment report.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    private void populateEnrollment(EnrollmentReportResponse r) {
        // Summary stats
        binding.tvTotalEnrolled.setText(String.valueOf(r.totalEnrolled));
        if (r.currentYear != null) {
            binding.tvAvgAttendanceEnr.setText(String.valueOf(r.currentYear.count));
        }

        // Year-over-year
        if (r.previousYear != null) {
            binding.tvPrevYear.setText(r.previousYear.year + ": " + r.previousYear.count);
        }
        if (r.currentYear != null) {
            binding.tvCurrYear.setText(r.currentYear.year + ": " + r.currentYear.count);
        }

        // By Gender — build pill rows
        binding.layoutGender.removeAllViews();
        if (r.byGender != null) {
            for (Map.Entry<String, Integer> e : r.byGender.entrySet()) {
                binding.layoutGender.addView(createPillRow(
                    capitalize(e.getKey()), String.valueOf(e.getValue())));
            }
        }

        // By Age Group
        binding.layoutAgeGroups.removeAllViews();
        if (r.byAgeGroup != null) {
            for (Map.Entry<String, Integer> e : r.byAgeGroup.entrySet()) {
                binding.layoutAgeGroups.addView(createPillRow(
                    e.getKey() + " years", String.valueOf(e.getValue())));
            }
        }

        // By Diagnosis
        binding.layoutDiagnosis.removeAllViews();
        if (r.byDiagnosis != null) {
            for (Map.Entry<String, Integer> e : r.byDiagnosis.entrySet()) {
                String label = e.getKey();
                if (label == null || label.equals("null") || label.isEmpty()) label = "None";
                binding.layoutDiagnosis.addView(createPillRow(
                    capitalize(label), String.valueOf(e.getValue())));
            }
        }
    }

    /** Creates a horizontal "label  …………  count" row */
    private View createPillRow(String label, String value) {
        LinearLayout row = new LinearLayout(this);
        row.setOrientation(LinearLayout.HORIZONTAL);
        row.setLayoutParams(new LinearLayout.LayoutParams(
            LinearLayout.LayoutParams.MATCH_PARENT,
            LinearLayout.LayoutParams.WRAP_CONTENT));
        row.setGravity(android.view.Gravity.CENTER_VERTICAL);

        TextView tvLabel = new TextView(this);
        tvLabel.setText(label);
        tvLabel.setTextSize(13);
        tvLabel.setTextColor(Color.parseColor("#616161"));
        tvLabel.setLayoutParams(new LinearLayout.LayoutParams(
            0, LinearLayout.LayoutParams.WRAP_CONTENT, 1f));

        TextView tvValue = new TextView(this);
        tvValue.setText(value);
        tvValue.setTextSize(14);
        tvValue.setTextColor(Color.parseColor("#2E7D32"));
        tvValue.setTypeface(tvValue.getTypeface(), android.graphics.Typeface.BOLD);

        row.addView(tvLabel);
        row.addView(tvValue);
        return row;
    }

    // ── Attendance Report ──────────────────────────────────────────

    private void loadAttendanceReport() {
        binding.progressBarAttendance.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getAttendanceReport(selectedMonth, null)
            .enqueue(new Callback<AttendanceReportResponse>() {
                @Override
                public void onResponse(Call<AttendanceReportResponse> call,
                                       Response<AttendanceReportResponse> response) {
                    binding.progressBarAttendance.setVisibility(View.GONE);
                    binding.swipeRefreshAttendance.setRefreshing(false);
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.code() == 403) {
                        Toast.makeText(ReportsActivity.this,
                            "Reports are available to administrators only.",
                            Toast.LENGTH_LONG).show();
                        return;
                    }
                    if (response.isSuccessful() && response.body() != null) {
                        populateAttendance(response.body());
                    }
                }
                @Override
                public void onFailure(Call<AttendanceReportResponse> call, Throwable t) {
                    binding.progressBarAttendance.setVisibility(View.GONE);
                    binding.swipeRefreshAttendance.setRefreshing(false);
                    Toast.makeText(ReportsActivity.this,
                        "Could not load attendance report.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    private void populateAttendance(AttendanceReportResponse r) {
        binding.tvAvgAttendance.setText(r.avgAttendance != null ? r.avgAttendance : "—");
        binding.tvReportTotalChildren.setText(String.valueOf(r.totalChildren));

        attendanceItems.clear();
        if (r.summary != null) attendanceItems.addAll(r.summary);
        attendanceAdapter.notifyDataSetChanged();

        boolean empty = attendanceItems.isEmpty();
        binding.tvEmptyAttendance.setVisibility(empty ? View.VISIBLE : View.GONE);
        binding.rvAttendanceReport.setVisibility(empty ? View.GONE : View.VISIBLE);
    }

    private void shiftReportMonth(int delta) {
        try {
            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM", Locale.getDefault());
            Calendar cal = Calendar.getInstance();
            cal.setTime(sdf.parse(selectedMonth));
            cal.add(Calendar.MONTH, delta);
            selectedMonth = sdf.format(cal.getTime());
            updateReportMonthLabel();
            loadAttendanceReport();
        } catch (Exception ignored) {}
    }

    private void updateReportMonthLabel() {
        try {
            SimpleDateFormat inFmt  = new SimpleDateFormat("yyyy-MM", Locale.getDefault());
            SimpleDateFormat outFmt = new SimpleDateFormat("MMMM yyyy", Locale.getDefault());
            binding.tvReportMonth.setText(outFmt.format(inFmt.parse(selectedMonth)));
        } catch (Exception e) {
            binding.tvReportMonth.setText(selectedMonth);
        }
    }

    private String capitalize(String s) {
        if (s == null || s.isEmpty()) return "—";
        return Character.toUpperCase(s.charAt(0)) + s.substring(1);
    }
}
