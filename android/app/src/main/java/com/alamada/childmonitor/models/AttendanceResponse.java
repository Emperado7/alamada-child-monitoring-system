package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class AttendanceResponse {
    @SerializedName("data")  public List<AttendanceRecord> data;
    @SerializedName("total") public int total;
}
