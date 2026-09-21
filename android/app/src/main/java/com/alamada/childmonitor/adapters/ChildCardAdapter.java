package com.alamada.childmonitor.adapters;

import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.models.Child;

import java.util.List;

public class ChildCardAdapter extends RecyclerView.Adapter<ChildCardAdapter.ViewHolder> {

    public interface OnChildClickListener {
        void onChildClick(Child child);
    }

    private final List<Child> children;
    private final OnChildClickListener listener;

    public ChildCardAdapter(List<Child> children, OnChildClickListener listener) {
        this.children = children;
        this.listener = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext())
            .inflate(R.layout.item_child_card, parent, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder h, int position) {
        Child child = children.get(position);

        String name = child.getDisplayName();
        h.tvName.setText(name);
        h.tvAge.setText("Age " + child.age);
        h.tvGender.setText(capitalize(child.gender));
        h.tvDiagnosis.setText(child.diagnosis != null ? child.diagnosis : "—");

        // Avatar initial
        if (!name.isEmpty()) {
            h.tvInitial.setText(String.valueOf(name.charAt(0)).toUpperCase());
        }

        // Status badge color
        String status = child.status != null ? child.status : "";
        h.tvStatus.setText(capitalize(status));
        switch (status) {
            case "active":
                h.tvStatus.setTextColor(Color.parseColor("#2E7D32"));
                h.tvStatus.getBackground().setTint(Color.parseColor("#E8F5E9"));
                break;
            case "graduated":
                h.tvStatus.setTextColor(Color.parseColor("#0277BD"));
                h.tvStatus.getBackground().setTint(Color.parseColor("#E1F5FE"));
                break;
            default:
                h.tvStatus.setTextColor(Color.parseColor("#616161"));
                h.tvStatus.getBackground().setTint(Color.parseColor("#EEEEEE"));
        }

        h.card.setOnClickListener(v -> listener.onChildClick(child));
    }

    @Override
    public int getItemCount() { return children.size(); }

    private String capitalize(String s) {
        if (s == null || s.isEmpty()) return "—";
        return Character.toUpperCase(s.charAt(0)) + s.substring(1);
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        CardView card;
        TextView tvInitial, tvName, tvAge, tvGender, tvDiagnosis, tvStatus;

        ViewHolder(View v) {
            super(v);
            card        = v.findViewById(R.id.card_child);
            tvInitial   = v.findViewById(R.id.tv_child_initial);
            tvName      = v.findViewById(R.id.tv_child_name);
            tvAge       = v.findViewById(R.id.tv_child_age);
            tvGender    = v.findViewById(R.id.tv_child_gender);
            tvDiagnosis = v.findViewById(R.id.tv_child_diagnosis);
            tvStatus    = v.findViewById(R.id.tv_child_status);
        }
    }
}
