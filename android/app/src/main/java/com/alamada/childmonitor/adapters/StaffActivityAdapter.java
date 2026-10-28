package com.alamada.childmonitor.adapters;

import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.models.ActivityRecord;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;

/**
 * Adapter for the staff Activity Log screen.
 * Displays activity records with date, type, status badge, and notes.
 */
public class StaffActivityAdapter extends RecyclerView.Adapter<StaffActivityAdapter.ViewHolder> {

    private final List<ActivityRecord> activities;

    public StaffActivityAdapter(List<ActivityRecord> activities) {
        this.activities = activities;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext())
            .inflate(R.layout.item_staff_activity, parent, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder h, int position) {
        ActivityRecord act = activities.get(position);

        // Date block
        try {
            SimpleDateFormat inFmt = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
            Date date = inFmt.parse(act.activityDate);
            h.tvDayNum.setText(new SimpleDateFormat("dd", Locale.getDefault()).format(date));
            h.tvMonthShort.setText(new SimpleDateFormat("MMM", Locale.getDefault()).format(date).toUpperCase());
        } catch (ParseException e) {
            h.tvDayNum.setText("—");
            h.tvMonthShort.setText("");
        }

        // Activity type
        String type = act.activityType != null ? act.activityType : "—";
        h.tvType.setText(capitalize(type));

        // Notes
        if (act.notes != null && !act.notes.isEmpty()) {
            h.tvNotes.setText(act.notes);
            h.tvNotes.setVisibility(View.VISIBLE);
        } else {
            h.tvNotes.setVisibility(View.GONE);
        }

        // Status badge
        String status = act.completionStatus != null ? act.completionStatus : "";
        h.tvStatus.setText(status.replace("_", " "));

        int bgColor, textColor;
        switch (status) {
            case "completed":
                bgColor   = Color.parseColor("#E8F5E9");
                textColor = Color.parseColor("#2E7D32");
                break;
            case "in_progress":
                bgColor   = Color.parseColor("#FFF8E1");
                textColor = Color.parseColor("#E65100");
                break;
            case "not_started":
                bgColor   = Color.parseColor("#FFEBEE");
                textColor = Color.parseColor("#C62828");
                break;
            default:
                bgColor   = Color.parseColor("#F5F5F5");
                textColor = Color.GRAY;
        }
        h.tvStatus.setTextColor(textColor);
        h.tvStatus.getBackground().setTint(bgColor);
    }

    @Override
    public int getItemCount() { return activities.size(); }

    private String capitalize(String s) {
        if (s == null || s.isEmpty()) return "—";
        return Character.toUpperCase(s.charAt(0)) + s.substring(1).toLowerCase();
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        TextView tvDayNum, tvMonthShort, tvType, tvNotes, tvStatus;

        ViewHolder(View v) {
            super(v);
            tvDayNum    = v.findViewById(R.id.tv_day_num);
            tvMonthShort= v.findViewById(R.id.tv_month_short);
            tvType      = v.findViewById(R.id.tv_activity_type);
            tvNotes     = v.findViewById(R.id.tv_activity_notes);
            tvStatus    = v.findViewById(R.id.tv_status);
        }
    }
}
