package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class ChildListResponse {
    @SerializedName("data")  public List<Child> data;
    @SerializedName("total") public int total;
}
