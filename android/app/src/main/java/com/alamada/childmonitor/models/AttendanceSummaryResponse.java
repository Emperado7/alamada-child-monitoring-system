package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class AttendanceSummaryResponse {
    @SerializedName("month")   public String       month;
    @SerializedName("summary") public List<Summary> summary;

    public static class Summary {
        @SerializedName("child_id")        public int    childId;
        @SerializedName("child_name")      public String childName;
        @SerializedName("present")         public int    present;
        @SerializedName("absent")          public int    absent;
        @SerializedName("late")            public int    late;
        @SerializedName("excused")         public int    excused;
        @SerializedName("total_days")      public int    totalDays;
        @SerializedName("attendance_rate") public String attendanceRate;
    }
}
