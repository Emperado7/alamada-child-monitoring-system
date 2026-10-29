package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import com.alamada.childmonitor.databinding.ActivityChildDetailBinding;
import com.alamada.childmonitor.models.Child;
import com.alamada.childmonitor.models.ChildListResponse;
import com.alamada.childmonitor.models.ChildResponse;
import com.alamada.childmonitor.network.ApiClient;
import com.bumptech.glide.Glide;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Child Information screen — shows child profile in the header and
 * 3 menu cards: Attendance Record, Daily Activities, Notifications.
 * Matches the design mockup exactly.
 */
public class ChildDetailActivity extends BaseActivity {

    private ActivityChildDetailBinding binding;
    private int childId = -1;
    private List<Child> childList = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityChildDetailBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        childId = getIntent().getIntExtra("child_id", -1);

        // Back button
        binding.btnBack.setOnClickListener(v -> finish());

        // Switch child button (shown only when >1 child)
        binding.btnSwitchChild.setOnClickListener(v -> showChildSwitcher());

        // ── Card navigation ─────────────────────────────────────────
        binding.cardAttendance.setOnClickListener(v -> {
            Intent i = new Intent(this, AttendanceActivity.class);
            i.putExtra("child_id", childId);
            startActivity(i);
        });

        binding.cardActivities.setOnClickListener(v -> {
            Intent i = new Intent(this, ActivitiesActivity.class);
            i.putExtra("child_id", childId);
            startActivity(i);
        });

        binding.cardNotifications.setOnClickListener(v ->
            startActivity(new Intent(this, NotificationsActivity.class)));

        // ── Bottom nav ───────────────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this, DashboardActivity.class));
            finish();
        });
        binding.tabSettings.setOnClickListener(v ->
            startActivity(new Intent(this, SettingsActivity.class)));

        loadAllChildren();
    }

    // ── Step 1: load child list so switcher and default selection work ──

    private void loadAllChildren() {
        binding.progressBar.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getMyChildren()
            .enqueue(new Callback<ChildListResponse>() {
                @Override
                public void onResponse(Call<ChildListResponse> call,
                                       Response<ChildListResponse> response) {
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.isSuccessful() && response.body() != null
                            && response.body().data != null) {
                        childList = response.body().data;

                        // Show switch button only when more than one child
                        binding.btnSwitchChild.setVisibility(
                            childList.size() > 1 ? View.VISIBLE : View.GONE);

                        // Pick the child: intent extra, or first in list
                        if (childId == -1 && !childList.isEmpty()) {
                            childId = childList.get(0).id;
                        }

                        if (childId != -1) {
                            loadChildDetail(childId);
                        } else {
                            binding.progressBar.setVisibility(View.GONE);
                            Toast.makeText(ChildDetailActivity.this,
                                "No children linked to your account.", Toast.LENGTH_LONG).show();
                            finish();
                        }
                    } else {
                        binding.progressBar.setVisibility(View.GONE);
                    }
                }
                @Override
                public void onFailure(Call<ChildListResponse> call, Throwable t) {
                    binding.progressBar.setVisibility(View.GONE);
                    Toast.makeText(ChildDetailActivity.this,
                        "Network error.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    // ── Step 2: load selected child's detail ────────────────────────────

    private void loadChildDetail(int id) {
        binding.progressBar.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getChildDetail(id)
            .enqueue(new Callback<ChildResponse>() {
                @Override
                public void onResponse(Call<ChildResponse> call,
                                       Response<ChildResponse> response) {
                    binding.progressBar.setVisibility(View.GONE);
                    if (response.code() == 401) { handleUnauthorised(); return; }
                    if (response.isSuccessful() && response.body() != null) {
                        populateHeader(response.body());
                    } else {
                        Toast.makeText(ChildDetailActivity.this,
                            "Could not load child information.", Toast.LENGTH_SHORT).show();
                    }
                }
                @Override
                public void onFailure(Call<ChildResponse> call, Throwable t) {
                    binding.progressBar.setVisibility(View.GONE);
                    Toast.makeText(ChildDetailActivity.this,
                        "Network error.", Toast.LENGTH_SHORT).show();
                }
            });
    }

    // ── Populate the green header ─────────────────────────────────────────

    private void populateHeader(ChildResponse c) {
        // Full name
        String name = (c.fullName != null && !c.fullName.isEmpty())
            ? c.fullName
            : trim(c.firstName) + " " + trim(c.lastName);
        name = name.trim();
        binding.tvName.setText(name.isEmpty() ? "—" : name);

        // Status chip — show diagnosis if available, else enrollment status
        String chip = (c.diagnosis != null && !c.diagnosis.isEmpty())
            ? capitalize(c.diagnosis)
            : capitalize(c.status);
        binding.tvStatus.setText(chip.isEmpty() ? "Enrolled" : chip);

        // Avatar photo or initial
        if (c.photo != null && !c.photo.isEmpty()) {
            binding.ivPhoto.setVisibility(View.VISIBLE);
            binding.tvPhotoInitial.setVisibility(View.GONE);
            Glide.with(this).load(c.photo).circleCrop().into(binding.ivPhoto);
        } else {
            binding.ivPhoto.setVisibility(View.GONE);
            binding.tvPhotoInitial.setVisibility(View.VISIBLE);
            if (!name.isEmpty()) {
                binding.tvPhotoInitial.setText(
                    String.valueOf(name.charAt(0)).toUpperCase());
                binding.tvPhotoInitial.setTextSize(32);
                binding.tvPhotoInitial.setTextColor(
                    getResources().getColor(com.alamada.childmonitor.R.color.primary, null));
            }
        }

        // Pass child_id to attendance/activities cards
        childId = c.id;
    }

    // ── Child switcher dialog ─────────────────────────────────────────────

    private void showChildSwitcher() {
        if (childList.isEmpty()) return;
        String[] names = new String[childList.size()];
        for (int i = 0; i < childList.size(); i++) {
            names[i] = childList.get(i).getDisplayName();
        }
        new android.app.AlertDialog.Builder(this)
            .setTitle("Select Child")
            .setItems(names, (dialog, which) -> {
                int selected = childList.get(which).id;
                if (selected != childId) {
                    childId = selected;
                    loadChildDetail(childId);
                }
            })
            .show();
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private String trim(String s) { return s != null ? s.trim() : ""; }

    private String capitalize(String s) {
        if (s == null || s.trim().isEmpty()) return "";
        return Character.toUpperCase(s.charAt(0)) + s.substring(1);
    }
}
