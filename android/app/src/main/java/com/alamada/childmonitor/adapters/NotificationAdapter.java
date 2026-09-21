package com.alamada.childmonitor.adapters;

import android.graphics.Color;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.models.Notification;

import java.util.List;

public class NotificationAdapter extends RecyclerView.Adapter<NotificationAdapter.ViewHolder> {

    public interface OnNotificationClickListener {
        void onNotificationClick(Notification n);
    }

    private final List<Notification> notifications;
    private final OnNotificationClickListener listener;

    public NotificationAdapter(List<Notification> notifications,
                                OnNotificationClickListener listener) {
        this.notifications = notifications;
        this.listener      = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext())
            .inflate(R.layout.item_notification, parent, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder h, int position) {
        Notification n = notifications.get(position);

        h.tvTitle.setText(n.title != null ? n.title : "—");
        h.tvMessage.setText(n.message != null ? n.message : "");

        // Date
        String dateStr = n.sentAt != null
            ? n.sentAt.substring(0, Math.min(10, n.sentAt.length()))
            : "";
        h.tvDate.setText(dateStr);

        // Unread styling
        if (!n.isRead) {
            h.tvTitle.setTypeface(null, Typeface.BOLD);
            h.card.setCardBackgroundColor(Color.parseColor("#EEF5FF"));
            h.viewUnreadDot.setVisibility(View.VISIBLE);
            h.viewUnreadDot.getBackground().setTint(Color.parseColor("#1565C0"));
        } else {
            h.tvTitle.setTypeface(null, Typeface.NORMAL);
            h.card.setCardBackgroundColor(Color.WHITE);
            h.viewUnreadDot.setVisibility(View.INVISIBLE);
        }

        h.card.setOnClickListener(v -> listener.onNotificationClick(n));
    }

    @Override
    public int getItemCount() { return notifications.size(); }

    static class ViewHolder extends RecyclerView.ViewHolder {
        CardView card;
        TextView tvTitle, tvMessage, tvDate;
        View viewUnreadDot;

        ViewHolder(View v) {
            super(v);
            card          = v.findViewById(R.id.card_notification);
            tvTitle       = v.findViewById(R.id.tv_notification_title);
            tvMessage     = v.findViewById(R.id.tv_notification_message);
            tvDate        = v.findViewById(R.id.tv_notification_date);
            viewUnreadDot = v.findViewById(R.id.view_unread_dot);
        }
    }
}
