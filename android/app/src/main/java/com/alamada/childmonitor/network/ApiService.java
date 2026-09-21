package com.alamada.childmonitor.network;

import com.alamada.childmonitor.models.ActivityListResponse;
import com.alamada.childmonitor.models.AttendanceResponse;
import com.alamada.childmonitor.models.AttendanceSummaryResponse;
import com.alamada.childmonitor.models.AuthResponse;
import com.alamada.childmonitor.models.ChildListResponse;
import com.alamada.childmonitor.models.ChildResponse;
import com.alamada.childmonitor.models.EnrollmentListResponse;
import com.alamada.childmonitor.models.MessageResponse;
import com.alamada.childmonitor.models.NotificationListResponse;
import com.alamada.childmonitor.models.UnreadCountResponse;

import java.util.Map;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;
import retrofit2.http.Query;

public interface ApiService {

    // ── Auth ──────────────────────────────────────────────────────

    @POST("login")
    Call<AuthResponse> login(@Body Map<String, String> credentials);

    @POST("logout")
    Call<MessageResponse> logout();

    // ── Parent: Children ──────────────────────────────────────────

    @GET("parent/children")
    Call<ChildListResponse> getMyChildren();

    @GET("parent/children/{id}")
    Call<ChildResponse> getChildDetail(@Path("id") int childId);

    // ── Parent: Activities ────────────────────────────────────

    @GET("parent/children/{id}/activities")
    Call<ActivityListResponse> getActivities(@Path("id") int childId);

    // ── Parent: Attendance ────────────────────────────────────────

    @GET("parent/attendance")
    Call<AttendanceResponse> getAttendance(
            @Query("child_id") int childId,
            @Query("month") String month      // YYYY-MM
    );

    @GET("parent/attendance/summary")
    Call<AttendanceSummaryResponse> getAttendanceSummary(@Query("month") String month);

    // ── Parent: Enrollment ────────────────────────────────────────

    @GET("parent/enrollments")
    Call<EnrollmentListResponse> getEnrollments();

    // ── Notifications (all roles) ─────────────────────────────────

    @GET("notifications")
    Call<NotificationListResponse> getNotifications();

    @GET("notifications/unread-count")
    Call<UnreadCountResponse> getUnreadCount();

    @POST("notifications/{id}/read")
    Call<MessageResponse> markNotificationRead(@Path("id") int notificationId);

    @POST("notifications/read-all")
    Call<MessageResponse> markAllRead();

    // ── Profile ───────────────────────────────────────────────────

    @PUT("me/password")
    Call<MessageResponse> changePassword(@Body Map<String, String> body);
}
