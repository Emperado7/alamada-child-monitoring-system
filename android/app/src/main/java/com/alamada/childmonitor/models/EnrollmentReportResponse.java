package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;
import java.util.Map;

/**
 * Response from GET /api/admin/reports/enrollment
 */
public class EnrollmentReportResponse {

    @SerializedName("report_id")      public int    reportId;
    @SerializedName("generated_at")   public String generatedAt;
    @SerializedName("total_enrolled")  public int    totalEnrolled;

    @SerializedName("current_year")
    public YearCount currentYear;

    @SerializedName("previous_year")
    public YearCount previousYear;

    @SerializedName("by_gender")
    public Map<String, Integer> byGender;

    @SerializedName("by_diagnosis")
    public Map<String, Integer> byDiagnosis;

    @SerializedName("by_school_year")
    public Map<String, Integer> bySchoolYear;

    @SerializedName("by_age_group")
    public Map<String, Integer> byAgeGroup;

    @SerializedName("children")
    public List<EnrolledChild> children;

    public static class YearCount {
        @SerializedName("year")  public int year;
        @SerializedName("count") public int count;
    }

    public static class EnrolledChild {
        @SerializedName("id")           public int    id;
        @SerializedName("name")          public String name;
        @SerializedName("age")           public int    age;
        @SerializedName("gender")        public String gender;
        @SerializedName("diagnosis")     public String diagnosis;
        @SerializedName("school_year")    public String schoolYear;
        @SerializedName("enrolled_date") public String enrolledDate;
    }
}
