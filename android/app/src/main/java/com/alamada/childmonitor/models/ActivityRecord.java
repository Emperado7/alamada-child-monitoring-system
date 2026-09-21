package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;

public class ActivityRecord {
    @SerializedName("id")                public int    id;
    @SerializedName("child_id")          public int    childId;
    @SerializedName("activity_date")     public String activityDate;
    @SerializedName("activity_type")     public String activityType;
    @SerializedName("completion_status") public String completionStatus;
    @SerializedName("notes")             public String notes;

    public String getStatusDisplay() {
        if (completionStatus == null) return "—";
        return completionStatus.replace("_", " ");
    }
}
