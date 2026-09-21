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

public class ActivityAdapter extends RecyclerView.Adapter<ActivityAdapter.ViewHolder> {

    private final List<ActivityRecord> activities;

    public ActivityAdapter(List<ActivityRecord> activities) {
        this.activities = activities;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext())
            .inflate(R.layout.item_activity, parent, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder h, int position) {
        ActivityRecord act = activities.get(position);

        // Activity type
        h.tvType.setText(act.activityType != null ? act.activityType : "—");

        // Format date
        try {
            SimpleDateFormat inFmt  = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
            SimpleDateFormat outFmt = new SimpleDateFormat("EEEE, MMM dd, yyyy", Locale.getDefault());
            Date d = inFmt.parse(act.activityDate);
            h.tvDate.setText(outFmt.format(d));
        } catch (ParseException e) {
            h.tvDate.setText(act.activityDate != null ? act.activityDate : "—");
        }

        // Notes
        if (act.notes != null && !act.notes.isEmpty()) {
            h.tvNotes.setText(act.notes);
            h.tvNotes.setVisibility(View.VISIBLE);
        } else {
            h.tvNotes.setVisibility(View.GONE);
        }

        // Status badge
        String cs = act.completionStatus != null ? act.completionStatus : "";
        String label = cs.replace("_", " ");
        if (!label.isEmpty())
            label = Character.toUpperCase(label.charAt(0)) + label.substring(1);
        h.tvStatus.setText(label);

        int dotColor, bgColor, textColor;
        switch (cs) {
            case "completed":
                dotColor  = Color.parseColor("#2E7D32");
                bgColor   = Color.parseColor("#E8F5E9");
                textColor = Color.parseColor("#2E7D32");
                break;
            case "in_progress":
                dotColor  = Color.parseColor("#E65100");
                bgColor   = Color.parseColor("#FFF8E1");
                textColor = Color.parseColor("#E65100");
                break;
            default: // not_started
                dotColor  = Color.parseColor("#9E9E9E");
                bgColor   = Color.parseColor("#F5F5F5");
                textColor = Color.parseColor("#616161");
        }

        h.statusDot.getBackground().setTint(dotColor);
        h.tvStatus.setTextColor(textColor);
        h.tvStatus.getBackground().setTint(bgColor);
    }

    @Override
    public int getItemCount() { return activities.size(); }

    static class ViewHolder extends RecyclerView.ViewHolder {
        TextView tvType, tvDate, tvNotes, tvStatus;
        View statusDot;

        ViewHolder(View v) {
            super(v);
            tvType    = v.findViewById(R.id.tv_activity_type);
            tvDate    = v.findViewById(R.id.tv_activity_date);
            tvNotes   = v.findViewById(R.id.tv_activity_notes);
            tvStatus  = v.findViewById(R.id.tv_activity_status);
            statusDot = v.findViewById(R.id.view_status_dot);
        }
    }
}
