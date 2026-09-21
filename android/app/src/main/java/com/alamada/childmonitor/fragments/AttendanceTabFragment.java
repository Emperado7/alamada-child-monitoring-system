package com.alamada.childmonitor.fragments;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.AttendanceAdapter;
import com.alamada.childmonitor.models.AttendanceRecord;
import com.alamada.childmonitor.models.AttendanceResponse;
import com.alamada.childmonitor.models.AttendanceSummaryResponse;
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

public class AttendanceTabFragment extends Fragment {

    private static final String ARG_CHILD_ID = "child_id";

    private int childId;
    private String selectedMonth;
    private SessionManager session;
    private AttendanceAdapter adapter;
    private final List<AttendanceRecord> records = new ArrayList<>();

    private TextView tvMonth, tvPresent, tvAbsent, tvLate, tvRate, tvEmpty;
    private RecyclerView rvAttendance;
    private SwipeRefreshLayout swipeRefresh;

    public static AttendanceTabFragment newInstance(int childId) {
        AttendanceTabFragment f = new AttendanceTabFragment();
        Bundle args = new Bundle();
        args.putInt(ARG_CHILD_ID, childId);
        f.setArguments(args);
        return f;
    }

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) childId = getArguments().getInt(ARG_CHILD_ID);
        session = new SessionManager(requireContext());
        selectedMonth = new SimpleDateFormat("yyyy-MM", Locale.getDefault())
            .format(Calendar.getInstance().getTime());
    }

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater,
                             @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_tab_attendance, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        tvMonth      = view.findViewById(R.id.tv_month);
        tvPresent    = view.findViewById(R.id.tv_present);
        tvAbsent     = view.findViewById(R.id.tv_absent);
        tvLate       = view.findViewById(R.id.tv_late);
        tvRate       = view.findViewById(R.id.tv_rate);
        tvEmpty      = view.findViewById(R.id.tv_empty);
        rvAttendance = view.findViewById(R.id.rv_attendance);
        swipeRefresh = view.findViewById(R.id.swipe_refresh);

        view.findViewById(R.id.btn_prev_month).setOnClickListener(v -> shiftMonth(-1));
        view.findViewById(R.id.btn_next_month).setOnClickListener(v -> shiftMonth(+1));

        adapter = new AttendanceAdapter(records);
        rvAttendance.setLayoutManager(new LinearLayoutManager(requireContext()));
        rvAttendance.setAdapter(adapter);

        swipeRefresh.setColorSchemeResources(R.color.primary);
        swipeRefresh.setOnRefreshListener(this::loadAll);

        updateMonthLabel();
        loadAll();
    }

    // ── Data loading ──────────────────────────────────────────

    private void loadAll() {
        loadRecords();
        loadSummary();
    }

    private void loadRecords() {
        ApiClient.getInstance(session.getBearerToken())
            .getAttendance(childId, selectedMonth)
            .enqueue(new Callback<AttendanceResponse>() {
                @Override
                public void onResponse(Call<AttendanceResponse> call,
                                       Response<AttendanceResponse> response) {
                    swipeRefresh.setRefreshing(false);
                    if (response.isSuccessful() && response.body() != null) {
                        records.clear();
                        if (response.body().data != null) records.addAll(response.body().data);
                        adapter.notifyDataSetChanged();
                        tvEmpty.setVisibility(records.isEmpty() ? View.VISIBLE : View.GONE);
                        rvAttendance.setVisibility(records.isEmpty() ? View.GONE : View.VISIBLE);
                    }
                }
                @Override
                public void onFailure(Call<AttendanceResponse> call, Throwable t) {
                    swipeRefresh.setRefreshing(false);
                }
            });
    }

    private void loadSummary() {
        ApiClient.getInstance(session.getBearerToken())
            .getAttendanceSummary(selectedMonth)
            .enqueue(new Callback<AttendanceSummaryResponse>() {
                @Override
                public void onResponse(Call<AttendanceSummaryResponse> call,
                                       Response<AttendanceSummaryResponse> response) {
                    if (response.isSuccessful() && response.body() != null
                            && response.body().summary != null) {
                        for (AttendanceSummaryResponse.Summary s : response.body().summary) {
                            if (s.childId == childId) {
                                tvPresent.setText(String.valueOf(s.present));
                                tvAbsent.setText(String.valueOf(s.absent));
                                tvLate.setText(String.valueOf(s.late));
                                tvRate.setText(s.attendanceRate);
                                return;
                            }
                        }
                    }
                    // Reset if nothing found
                    tvPresent.setText("0");
                    tvAbsent.setText("0");
                    tvLate.setText("0");
                    tvRate.setText("0%");
                }
                @Override
                public void onFailure(Call<AttendanceSummaryResponse> call, Throwable t) { }
            });
    }

    // ── Month navigation ──────────────────────────────────────

    private void shiftMonth(int delta) {
        try {
            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM", Locale.getDefault());
            Calendar cal = Calendar.getInstance();
            cal.setTime(sdf.parse(selectedMonth));
            cal.add(Calendar.MONTH, delta);
            selectedMonth = sdf.format(cal.getTime());
            updateMonthLabel();
            loadAll();
        } catch (Exception ignored) { }
    }

    private void updateMonthLabel() {
        try {
            SimpleDateFormat inFmt  = new SimpleDateFormat("yyyy-MM", Locale.getDefault());
            SimpleDateFormat outFmt = new SimpleDateFormat("MMMM yyyy", Locale.getDefault());
            tvMonth.setText(outFmt.format(inFmt.parse(selectedMonth)));
        } catch (Exception e) {
            tvMonth.setText(selectedMonth);
        }
    }
}
