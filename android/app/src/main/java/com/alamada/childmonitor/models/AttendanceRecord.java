package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;

public class AttendanceRecord {
    @SerializedName("id")              public int    id;
    @SerializedName("child_id")        public int    childId;
    @SerializedName("attendance_date") public String attendanceDate;
    @SerializedName("status")          public String status;
    @SerializedName("remarks")         public String remarks;

    /** Returns a capitalized, display-friendly status string */
    public String getStatusDisplay() {
        if (status == null) return "—";
        return status.substring(0, 1).toUpperCase() + status.substring(1);
    }
}
