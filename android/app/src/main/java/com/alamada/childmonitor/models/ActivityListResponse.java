package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class ActivityListResponse {
    @SerializedName("data")  public List<ActivityRecord> data;
    @SerializedName("total") public int total;
}
