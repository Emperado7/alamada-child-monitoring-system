package com.alamada.childmonitor.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.models.Child;

import java.util.List;

/**
 * Adapter for the staff "Mark Attendance" screen.
 * Each row shows a child with Present / Absent / Excused radio buttons.
 * Tracks the selected status per child and exposes it via {@link #getSelectedStatuses()}.
 */
public class StaffAttendanceAdapter extends RecyclerView.Adapter<StaffAttendanceAdapter.ViewHolder> {

    private final List<Child> children;
    /** Status per child: "present" / "absent" / "excused" */
    private final String[] statuses;

    public StaffAttendanceAdapter(List<Child> children) {
        this.children  = children;
        this.statuses  = new String[children.size()];
        // Default everyone to "present"
        java.util.Arrays.fill(statuses, "present");
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext())
            .inflate(R.layout.item_staff_attendance, parent, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder h, int position) {
        Child child = children.get(position);

        String name = child.getDisplayName();
        h.tvName.setText(name);
        h.tvInfo.setText("Age " + child.age + "  ·  " + capitalize(child.gender));

        if (!name.isEmpty()) {
            h.tvInitial.setText(String.valueOf(name.charAt(0)).toUpperCase());
        }

        // Set radio without triggering listener
        h.rgStatus.setOnCheckedChangeListener(null);
        String status = statuses[position] != null ? statuses[position] : "present";
        switch (status) {
            case "absent":  h.rgStatus.check(R.id.rb_absent);  break;
            case "excused":  h.rgStatus.check(R.id.rb_excused);  break;
            default:         h.rgStatus.check(R.id.rb_present);  break;
        }

        h.rgStatus.setOnCheckedChangeListener((group, checkedId) -> {
            int pos = h.getBindingAdapterPosition();
            if (pos == RecyclerView.NO_POSITION) return;
            if (checkedId == R.id.rb_present)       statuses[pos] = "present";
            else if (checkedId == R.id.rb_absent)   statuses[pos] = "absent";
            else if (checkedId == R.id.rb_excused)  statuses[pos] = "excused";
        });
    }

    @Override
    public int getItemCount() { return children.size(); }

    /**
     * Returns the status chosen for each child, aligned with the children list order.
     */
    public String[] getSelectedStatuses() { return statuses; }

    private String capitalize(String s) {
        if (s == null || s.isEmpty()) return "—";
        return Character.toUpperCase(s.charAt(0)) + s.substring(1);
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        TextView   tvInitial, tvName, tvInfo;
        RadioGroup  rgStatus;

        ViewHolder(View v) {
            super(v);
            tvInitial = v.findViewById(R.id.tv_child_initial);
            tvName    = v.findViewById(R.id.tv_child_name);
            tvInfo    = v.findViewById(R.id.tv_child_info);
            rgStatus  = v.findViewById(R.id.rg_status);
        }
    }
}
