package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;

public class Notification {
    @SerializedName("id")      public int     id;
    @SerializedName("title")   public String  title;
    @SerializedName("message") public String  message;
    @SerializedName("sent_at") public String  sentAt;
    @SerializedName("is_read") public boolean isRead;
}
