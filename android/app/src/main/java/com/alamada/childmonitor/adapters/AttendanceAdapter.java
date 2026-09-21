package com.alamada.childmonitor.adapters;

import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.models.AttendanceRecord;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;

public class AttendanceAdapter extends RecyclerView.Adapter<AttendanceAdapter.ViewHolder> {

    private final List<AttendanceRecord> records;

    public AttendanceAdapter(List<AttendanceRecord> records) {
        this.records = records;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext())
            .inflate(R.layout.item_attendance, parent, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder h, int position) {
        AttendanceRecord rec = records.get(position);

        // Parse and format date
        try {
            SimpleDateFormat inFmt  = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
            Date date = inFmt.parse(rec.attendanceDate);

            SimpleDateFormat dayNum  = new SimpleDateFormat("dd",   Locale.getDefault());
            SimpleDateFormat monShrt = new SimpleDateFormat("MMM",  Locale.getDefault());
            SimpleDateFormat dayName = new SimpleDateFormat("EEEE", Locale.getDefault());
            SimpleDateFormat full    = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault());

            h.tvDayNum.setText(dayNum.format(date));
            h.tvMonthShort.setText(monShrt.format(date).toUpperCase());
            h.tvDayName.setText(dayName.format(date));
            h.tvDate.setText(full.format(date));
        } catch (ParseException e) {
            h.tvDayNum.setText("—");
            h.tvMonthShort.setText("");
            h.tvDayName.setText(rec.attendanceDate);
            h.tvDate.setText("");
        }

        // Remarks
        if (rec.remarks != null && !rec.remarks.isEmpty()) {
            h.tvRemarks.setText(rec.remarks);
            h.tvRemarks.setVisibility(View.VISIBLE);
        } else {
            h.tvRemarks.setVisibility(View.GONE);
        }

        // Status badge + left bar color
        String status = rec.status != null ? rec.status : "";
        String label  = status.isEmpty() ? "—"
            : Character.toUpperCase(status.charAt(0)) + status.substring(1);
        h.tvStatus.setText(label);

        int barColor, bgColor, textColor;
        switch (status) {
            case "present":
                barColor = Color.parseColor("#2E7D32");
                bgColor  = Color.parseColor("#E8F5E9");
                textColor = Color.parseColor("#2E7D32");
                break;
            case "absent":
                barColor  = Color.parseColor("#C62828");
                bgColor   = Color.parseColor("#FFEBEE");
                textColor = Color.parseColor("#C62828");
                break;
            case "late":
                barColor  = Color.parseColor("#E65100");
                bgColor   = Color.parseColor("#FFF8E1");
                textColor = Color.parseColor("#E65100");
                break;
            case "excused":
                barColor  = Color.parseColor("#1565C0");
                bgColor   = Color.parseColor("#E3F2FD");
                textColor = Color.parseColor("#1565C0");
                break;
            default:
                barColor  = Color.GRAY;
                bgColor   = Color.parseColor("#F5F5F5");
                textColor = Color.GRAY;
        }

        h.viewStatusBar.getBackground().setTint(barColor);
        h.tvStatus.setTextColor(textColor);
        h.tvStatus.getBackground().setTint(bgColor);
    }

    @Override
    public int getItemCount() { return records.size(); }

    static class ViewHolder extends RecyclerView.ViewHolder {
        View viewStatusBar;
        TextView tvDayNum, tvMonthShort, tvDayName, tvDate, tvRemarks, tvStatus;

        ViewHolder(View v) {
            super(v);
            viewStatusBar = v.findViewById(R.id.view_status_bar);
            tvDayNum      = v.findViewById(R.id.tv_day_num);
            tvMonthShort  = v.findViewById(R.id.tv_month_short);
            tvDayName     = v.findViewById(R.id.tv_day_name);
            tvDate        = v.findViewById(R.id.tv_attendance_date);
            tvRemarks     = v.findViewById(R.id.tv_attendance_remarks);
            tvStatus      = v.findViewById(R.id.tv_attendance_status);
        }
    }
}
