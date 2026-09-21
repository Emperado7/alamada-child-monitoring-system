package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class EnrollmentListResponse {
    @SerializedName("data")  public List<Enrollment> data;
    @SerializedName("total") public int total;
}
