package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;

public class Enrollment {
    @SerializedName("id")              public int    id;
    @SerializedName("child_id")        public int    childId;
    @SerializedName("enrollment_date") public String enrollmentDate;
    @SerializedName("school_year")     public int    schoolYear;
    @SerializedName("status")          public String status;
    @SerializedName("remarks")         public String remarks;
    @SerializedName("child")           public Child  child;
}
