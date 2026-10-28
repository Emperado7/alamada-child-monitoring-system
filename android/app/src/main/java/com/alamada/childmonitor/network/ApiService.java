package com.alamada.childmonitor.network;

import com.alamada.childmonitor.models.ActivityListResponse;
import com.alamada.childmonitor.models.AttendanceReportResponse;
import com.alamada.childmonitor.models.AttendanceResponse;
import com.alamada.childmonitor.models.AttendanceSummaryResponse;
import com.alamada.childmonitor.models.AuthResponse;
import com.alamada.childmonitor.models.ChildListResponse;
import com.alamada.childmonitor.models.ChildResponse;
import com.alamada.childmonitor.models.EnrollmentListResponse;
import com.alamada.childmonitor.models.EnrollmentReportResponse;
import com.alamada.childmonitor.models.MessageResponse;
import com.alamada.childmonitor.models.NotificationListResponse;
import com.alamada.childmonitor.models.UnreadCountResponse;

import java.util.Map;

import okhttp3.ResponseBody;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;
import retrofit2.http.Query;

public interface ApiService {

    // ── Auth ──────────────────────────────────────────────────────

    /** Wake-up ping — hits /api/health to warm the Render instance before login */
    @GET("health")
    Call<okhttp3.ResponseBody> pingHealth();

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

    // ── Staff/Admin: Children ─────────────────────────────────────

    @GET("children")
    Call<ChildListResponse> getAllChildren();

    @GET("children/{id}")
    Call<ChildResponse> getChildById(@Path("id") int childId);

    // ── Staff/Admin: Attendance ───────────────────────────────────

    @GET("attendance")
    Call<AttendanceResponse> getStaffAttendance(
            @Query("child_id") int childId,
            @Query("month") String month
    );

    @GET("attendance/summary")
    Call<AttendanceSummaryResponse> getStaffAttendanceSummary(@Query("month") String month);

    @POST("attendance")
    Call<MessageResponse> recordAttendance(@Body Map<String, Object> body);

    @POST("attendance/bulk")
    Call<MessageResponse> recordBulkAttendance(@Body Map<String, Object> body);

    @PUT("attendance/{id}")
    Call<MessageResponse> updateAttendance(@Path("id") int id, @Body Map<String, String> body);

    // ── Staff/Admin: Activities ───────────────────────────────────

    @GET("activities")
    Call<ActivityListResponse> getAllActivities(
            @Query("child_id") int childId,
            @Query("month") String month
    );

    @POST("activities")
    Call<MessageResponse> logActivity(@Body Map<String, Object> body);

    @PUT("activities/{id}")
    Call<MessageResponse> updateActivity(@Path("id") int id, @Body Map<String, Object> body);

    @DELETE("activities/{id}")
    Call<MessageResponse> deleteActivity(@Path("id") int id);

    // ── Staff/Admin: Enrollments ──────────────────────────────────

    @GET("enrollments")
    Call<EnrollmentListResponse> getAllEnrollments();

    // ── Admin: Reports ────────────────────────────────────────────

    @GET("admin/reports/enrollment")
    Call<EnrollmentReportResponse> getEnrollmentReport(
            @Query("gender") String gender,
            @Query("school_year") String schoolYear,
            @Query("age_min") String ageMin,
            @Query("age_max") String ageMax
    );

    @GET("admin/reports/attendance")
    Call<AttendanceReportResponse> getAttendanceReport(
            @Query("month") String month,
            @Query("child_id") String childId
    );

    // ── Profile ───────────────────────────────────────────────────

    @PUT("me/password")
    Call<MessageResponse> changePassword(@Body Map<String, String> body);

    // ── FCM Device Token ─────────────────────────────────────────

    @POST("device-token")
    Call<MessageResponse> registerDeviceToken(@Body Map<String, String> body);
}
