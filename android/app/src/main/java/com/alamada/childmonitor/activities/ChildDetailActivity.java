package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.fragment.app.Fragment;
import androidx.viewpager2.adapter.FragmentStateAdapter;

import com.alamada.childmonitor.databinding.ActivityChildDetailBinding;
import com.alamada.childmonitor.fragments.ActivitiesFragment;
import com.alamada.childmonitor.fragments.AttendanceTabFragment;
import com.alamada.childmonitor.fragments.InfoFragment;
import com.alamada.childmonitor.models.Child;
import com.alamada.childmonitor.models.ChildListResponse;
import com.alamada.childmonitor.models.ChildResponse;
import com.alamada.childmonitor.network.ApiClient;
import com.bumptech.glide.Glide;
import com.google.android.material.tabs.TabLayoutMediator;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ChildDetailActivity extends BaseActivity {

    private ActivityChildDetailBinding binding;
    private int childId = -1;
    private ChildResponse childData;
    private List<Child> childList = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityChildDetailBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        childId = getIntent().getIntExtra("child_id", -1);

        binding.btnBack.setOnClickListener(v -> finish());

        // ── Bottom nav ─────────────────────────────────────────────
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this, DashboardActivity.class));
            finish();
        });
        binding.tabNotifications.setOnClickListener(v -> {
            startActivity(new Intent(this, NotificationsActivity.class));
            finish();
        });
        binding.tabSettings.setOnClickListener(v -> {
            startActivity(new Intent(this, SettingsActivity.class));
            finish();
        });

        // ── Child switcher button ──────────────────────────────────
        binding.btnSwitchChild.setOnClickListener(v -> showChildSwitcher());

        loadAllChildren();
    }

    // ── Step 1: load all children so switcher works ────────────────

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
                        // Show switcher only if more than one child
                        binding.btnSwitchChild.setVisibility(
                            childList.size() > 1 ? View.VISIBLE : View.GONE);

                        if (childId == -1 && !childList.isEmpty()) {
                            childId = childList.get(0).id;
                        }
                        if (childId != -1) {
                            loadChildDetail(childId);
                        } else {
                            binding.progressBar.setVisibility(View.GONE);
                            showNoChildMessage();
                        }
                    } else {
                        binding.progressBar.setVisibility(View.GONE);
                        showNoChildMessage();
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

    // ── Step 2: load the selected child's detail ───────────────────

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
                        childData = response.body();
                        populateHeader(childData);
                        setupTabs();
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

    // ── Child switcher dialog ──────────────────────────────────────

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

    // ── Populate green header ──────────────────────────────────────

    private void populateHeader(ChildResponse c) {
        // Full name
        String displayName = (c.fullName != null && !c.fullName.isEmpty())
            ? c.fullName
            : trim(c.firstName) + " " + trim(c.lastName);
        displayName = displayName.trim();

        binding.tvName.setText(displayName.isEmpty() ? "—" : displayName);
        binding.tvAge.setText("Age " + c.age + "  ·  " + capitalize(c.gender));
        binding.tvDiagnosis.setText(safe(c.diagnosis));

        // Guardian info
        binding.tvGuardian.setText("👥  " + safe(c.guardianName));
        binding.tvContact.setText("📞  " + safe(c.guardianContact));

        // Enrollment status chip
        binding.tvStatus.setText(capitalize(c.status));

        // Photo or initial
        if (!displayName.isEmpty()) {
            binding.tvPhotoInitial.setText(
                String.valueOf(displayName.charAt(0)).toUpperCase());
        }
        if (c.photo != null && !c.photo.isEmpty()) {
            binding.ivPhoto.setVisibility(View.VISIBLE);
            binding.tvPhotoInitial.setVisibility(View.GONE);
            Glide.with(this).load(c.photo).circleCrop().into(binding.ivPhoto);
        } else {
            binding.ivPhoto.setVisibility(View.GONE);
            binding.tvPhotoInitial.setVisibility(View.VISIBLE);
        }
    }

    // ── Setup ViewPager2 tabs ──────────────────────────────────────

    private void setupTabs() {
        final String[] TABS = {"Attendance", "Activities", "Info"};
        final int finalChildId = childId;

        binding.viewPager.setAdapter(new FragmentStateAdapter(this) {
            @Override public int getItemCount() { return 3; }
            @Override public Fragment createFragment(int position) {
                switch (position) {
                    case 0: return AttendanceTabFragment.newInstance(finalChildId);
                    case 1: return ActivitiesFragment.newInstance(finalChildId);
                    case 2: return InfoFragment.newInstance(childData);
                    default: return AttendanceTabFragment.newInstance(finalChildId);
                }
            }
        });

        new TabLayoutMediator(binding.tabLayout, binding.viewPager,
            (tab, pos) -> tab.setText(TABS[pos])).attach();
    }

    // ── Helpers ────────────────────────────────────────────────────

    private void showNoChildMessage() {
        Toast.makeText(this,
            "No children linked to your account. Contact the learning center.",
            Toast.LENGTH_LONG).show();
        finish();
    }

    private String safe(String s) {
        return (s != null && !s.trim().isEmpty()) ? s.trim() : "—";
    }

    private String trim(String s) {
        return s != null ? s.trim() : "";
    }

    private String capitalize(String s) {
        if (s == null || s.trim().isEmpty()) return "—";
        return Character.toUpperCase(s.charAt(0)) + s.substring(1).toLowerCase();
    }
}
