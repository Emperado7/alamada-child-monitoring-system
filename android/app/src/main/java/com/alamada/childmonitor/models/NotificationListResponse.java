package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class NotificationListResponse {
    @SerializedName("data")  public List<Notification> data;
    @SerializedName("total") public int total;
}
