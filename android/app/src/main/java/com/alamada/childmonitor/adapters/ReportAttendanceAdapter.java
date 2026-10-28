package com.alamada.childmonitor.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.models.AttendanceReportResponse;

import java.util.List;

/**
 * Adapter for the attendance report summary list in the Reports screen.
 * Shows each child's attendance breakdown (present / absent / late) and rate.
 */
public class ReportAttendanceAdapter extends RecyclerView.Adapter<ReportAttendanceAdapter.ViewHolder> {

    private final List<AttendanceReportResponse.ChildAttendance> items;

    public ReportAttendanceAdapter(List<AttendanceReportResponse.ChildAttendance> items) {
        this.items = items;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext())
            .inflate(R.layout.item_report_attendance, parent, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder h, int position) {
        AttendanceReportResponse.ChildAttendance item = items.get(position);

        String name = item.childName != null ? item.childName : "—";
        h.tvName.setText(name);
        if (!name.isEmpty() && !name.equals("—")) {
            h.tvInitial.setText(String.valueOf(name.charAt(0)).toUpperCase());
        }

        h.tvPresent.setText(String.valueOf(item.present));
        h.tvAbsent.setText(String.valueOf(item.absent));
        h.tvLate.setText(String.valueOf(item.late));
        h.tvRate.setText(item.attendanceRate != null ? item.attendanceRate : "0%");
    }

    @Override
    public int getItemCount() { return items.size(); }

    static class ViewHolder extends RecyclerView.ViewHolder {
        TextView tvInitial, tvName, tvPresent, tvAbsent, tvLate, tvRate;

        ViewHolder(View v) {
            super(v);
            tvInitial = v.findViewById(R.id.tv_child_initial);
            tvName    = v.findViewById(R.id.tv_child_name);
            tvPresent = v.findViewById(R.id.tv_present_count);
            tvAbsent  = v.findViewById(R.id.tv_absent_count);
            tvLate    = v.findViewById(R.id.tv_late_count);
            tvRate    = v.findViewById(R.id.tv_rate);
        }
    }
}
