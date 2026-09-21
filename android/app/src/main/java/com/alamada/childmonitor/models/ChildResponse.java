package com.alamada.childmonitor.models;

import com.google.gson.annotations.SerializedName;

public class ChildResponse {
    // The API returns the child object directly (not nested)
    @SerializedName("id")               public int    id;
    @SerializedName("first_name")       public String firstName;
    @SerializedName("last_name")        public String lastName;
    @SerializedName("full_name")        public String fullName;
    @SerializedName("age")              public int    age;
    @SerializedName("date_of_birth")    public String dateOfBirth;
    @SerializedName("gender")           public String gender;
    @SerializedName("address")          public String address;
    @SerializedName("guardian_name")    public String guardianName;
    @SerializedName("guardian_contact") public String guardianContact;
    @SerializedName("diagnosis")        public String diagnosis;
    @SerializedName("diagnosis_notes")  public String diagnosisNotes;
    @SerializedName("status")           public String status;
    @SerializedName("photo")            public String photo;
}
