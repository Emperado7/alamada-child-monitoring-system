package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;

public class AuthResponse {

    @SerializedName("message") public String message;
    @SerializedName("token")   public String token;
    @SerializedName("user")    public UserInfo user;

    public static class UserInfo {
        @SerializedName("id")    public int    id;
        @SerializedName("name")  public String name;
        @SerializedName("email") public String email;
        @SerializedName("role")  public String role;
    }
}
