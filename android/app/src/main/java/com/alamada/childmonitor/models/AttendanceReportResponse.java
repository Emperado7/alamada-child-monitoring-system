package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

/**
 * Response from GET /api/admin/reports/attendance?month=YYYY-MM
 */
public class AttendanceReportResponse {

    @SerializedName("month")           public String month;
    @SerializedName("total_children")  public int    totalChildren;
    @SerializedName("avg_attendance")   public String avgAttendance;
    @SerializedName("summary")         public List<ChildAttendance> summary;

    public static class ChildAttendance {
        @SerializedName("child_id")        public int    childId;
        @SerializedName("child_name")      public String childName;
        @SerializedName("gender")          public String gender;
        @SerializedName("diagnosis")       public String diagnosis;
        @SerializedName("present")         public int    present;
        @SerializedName("absent")          public int    absent;
        @SerializedName("late")            public int    late;
        @SerializedName("excused")         public int    excused;
        @SerializedName("total_days")      public int    totalDays;
        @SerializedName("attendance_rate") public String attendanceRate;
    }
}
