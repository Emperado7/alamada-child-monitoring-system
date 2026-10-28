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

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;

public class NotificationAdapter extends RecyclerView.Adapter<NotificationAdapter.ViewHolder> {

    public interface OnNotificationClickListener {
        void onNotificationClick(Notification n);
    }

    private final List<Notification>         notifications;
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

        // Format sentAt — backend returns ISO-8601 e.g. "2024-05-01T08:30:00.000000Z"
        h.tvDate.setText(formatDate(n.sentAt));

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

    // ── Helpers ───────────────────────────────────────────────────

    private String formatDate(String raw) {
        if (raw == null || raw.isEmpty()) return "";
        // Try full ISO-8601 first, then plain date
        String[] patterns = {
            "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'",
            "yyyy-MM-dd'T'HH:mm:ss'Z'",
            "yyyy-MM-dd HH:mm:ss",
            "yyyy-MM-dd"
        };
        SimpleDateFormat out = new SimpleDateFormat("MMM d, yyyy  h:mm a", Locale.getDefault());
        for (String pattern : patterns) {
            try {
                Date d = new SimpleDateFormat(pattern, Locale.getDefault()).parse(raw);
                return out.format(d);
            } catch (ParseException ignored) { }
        }
        // Fallback: return the first 10 chars (date portion)
        return raw.length() >= 10 ? raw.substring(0, 10) : raw;
    }

    // ── ViewHolder ────────────────────────────────────────────────

    static class ViewHolder extends RecyclerView.ViewHolder {
        CardView card;
        TextView tvTitle, tvMessage, tvDate;
        View     viewUnreadDot;

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
