package com.alamada.childmonitor.adapters;

import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.models.Enrollment;

import java.util.List;

public class EnrollmentAdapter extends RecyclerView.Adapter<EnrollmentAdapter.ViewHolder> {

    private final List<Enrollment> items;

    public EnrollmentAdapter(List<Enrollment> items) {
        this.items = items;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext())
            .inflate(R.layout.item_enrollment, parent, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder h, int position) {
        Enrollment e = items.get(position);

        // Child name
        String childName = (e.child != null) ? e.child.getDisplayName() : "—";
        h.tvChildName.setText(childName);

        // School year — backend returns e.g. 2024, display as "S.Y. 2024 – 2025"
        if (e.schoolYear > 0) {
            h.tvSchoolYear.setText("S.Y. " + e.schoolYear + " – " + (e.schoolYear + 1));
        } else {
            h.tvSchoolYear.setText("School Year —");
        }

        // Enrollment date
        h.tvEnrollmentDate.setText(
            (e.enrollmentDate != null && !e.enrollmentDate.isEmpty())
                ? "Enrolled: " + e.enrollmentDate
                : "Enrolled: —");

        // Status badge — backend values: pending | approved | rejected
        String status = e.status != null ? e.status.toLowerCase() : "";
        String label  = status.isEmpty() ? "—"
            : Character.toUpperCase(status.charAt(0)) + status.substring(1);
        h.tvStatus.setText(label);

        int bgColor, textColor;
        switch (status) {
            case "approved":
                bgColor   = Color.parseColor("#E8F5E9");
                textColor = Color.parseColor("#2E7D32");
                break;
            case "pending":
                bgColor   = Color.parseColor("#FFF8E1");
                textColor = Color.parseColor("#E65100");
                break;
            case "rejected":
                bgColor   = Color.parseColor("#FFEBEE");
                textColor = Color.parseColor("#C62828");
                break;
            default:
                bgColor   = Color.parseColor("#F5F5F5");
                textColor = Color.parseColor("#616161");
        }
        // Use setTint so the badge_green_bg shape shows the right colour
        h.tvStatus.getBackground().setTint(bgColor);
        h.tvStatus.setTextColor(textColor);

        // Remarks
        if (e.remarks != null && !e.remarks.isEmpty()) {
            h.tvRemarks.setVisibility(View.VISIBLE);
            h.tvRemarks.setText(e.remarks);
        } else {
            h.tvRemarks.setVisibility(View.GONE);
        }
    }

    @Override
    public int getItemCount() { return items.size(); }

    static class ViewHolder extends RecyclerView.ViewHolder {
        TextView tvChildName, tvSchoolYear, tvEnrollmentDate, tvStatus, tvRemarks;

        ViewHolder(View v) {
            super(v);
            tvChildName      = v.findViewById(R.id.tv_child_name);
            tvSchoolYear     = v.findViewById(R.id.tv_school_year);
            tvEnrollmentDate = v.findViewById(R.id.tv_enrollment_date);
            tvStatus         = v.findViewById(R.id.tv_status);
            tvRemarks        = v.findViewById(R.id.tv_remarks);
        }
    }
}
