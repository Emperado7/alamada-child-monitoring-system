package com.alamada.childmonitor.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
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
import com.alamada.childmonitor.utils.SessionManager;
import com.bumptech.glide.Glide;
import com.google.android.material.tabs.TabLayoutMediator;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ChildDetailActivity extends AppCompatActivity {

    private ActivityChildDetailBinding binding;
    private SessionManager session;
    private int childId = -1;
    private ChildResponse childData;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityChildDetailBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        if (getSupportActionBar() != null) getSupportActionBar().hide();

        session = new SessionManager(this);

        // Get child_id from intent (may be -1 if opened from dashboard — load first child)
        childId = getIntent().getIntExtra("child_id", -1);

        // Back button
        binding.btnBack.setOnClickListener(v -> finish());

        // Bottom nav
        binding.tabHome.setOnClickListener(v -> {
            startActivity(new Intent(this, DashboardActivity.class));
            finish();
        });
        binding.tabSettings.setOnClickListener(v -> {
            startActivity(new Intent(this, SettingsActivity.class));
        });

        if (childId == -1) {
            // Load first child automatically
            loadFirstChild();
        } else {
            loadChildDetail(childId);
        }
    }

    // ── If no child_id passed, fetch first child ──────────

    private void loadFirstChild() {
        binding.progressBar.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getMyChildren()
            .enqueue(new Callback<ChildListResponse>() {
                @Override
                public void onResponse(Call<ChildListResponse> call,
                                       Response<ChildListResponse> response) {
                    if (response.isSuccessful() && response.body() != null) {
                        List<Child> list = response.body().data;
                        if (list != null && !list.isEmpty()) {
                            childId = list.get(0).id;
                            loadChildDetail(childId);
                        } else {
                            binding.progressBar.setVisibility(View.GONE);
                            showNoChildMessage();
                        }
                    } else if (response.code() == 401) {
                        sessionExpired();
                    } else {
                        binding.progressBar.setVisibility(View.GONE);
                        showNoChildMessage();
                    }
                }
                @Override
                public void onFailure(Call<ChildListResponse> call, Throwable t) {
                    binding.progressBar.setVisibility(View.GONE);
                    Toast.makeText(ChildDetailActivity.this,
                        "Network error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                }
            });
    }

    // ── Load a specific child ─────────────────────────────

    private void loadChildDetail(int id) {
        binding.progressBar.setVisibility(View.VISIBLE);
        ApiClient.getInstance(session.getBearerToken())
            .getChildDetail(id)
            .enqueue(new Callback<ChildResponse>() {
                @Override
                public void onResponse(Call<ChildResponse> call,
                                       Response<ChildResponse> response) {
                    binding.progressBar.setVisibility(View.GONE);
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

    // ── Populate the green header ─────────────────────────

    private void populateHeader(ChildResponse c) {
        String displayName = (c.fullName != null && !c.fullName.isEmpty())
            ? c.fullName
            : (c.firstName + " " + c.lastName);

        binding.tvName.setText(displayName);
        binding.tvAge.setText("Age " + c.age + "  ·  " + capitalize(c.gender));
        binding.tvDiagnosis.setText(c.diagnosis != null ? c.diagnosis : "—");
        binding.tvGuardian.setText(c.guardianName != null ? c.guardianName : "—");
        binding.tvContact.setText(c.guardianContact != null ? c.guardianContact : "—");
        binding.tvStatus.setText(capitalize(c.status));

        // Avatar initial
        if (!displayName.isEmpty()) {
            binding.tvPhotoInitial.setText(
                String.valueOf(displayName.charAt(0)).toUpperCase());
        }

        // Load photo if available
        if (c.photo != null && !c.photo.isEmpty()) {
            binding.ivPhoto.setVisibility(View.VISIBLE);
            binding.tvPhotoInitial.setVisibility(View.GONE);
            Glide.with(this)
                .load(c.photo)
                .circleCrop()
                .into(binding.ivPhoto);
        } else {
            binding.ivPhoto.setVisibility(View.GONE);
            binding.tvPhotoInitial.setVisibility(View.VISIBLE);
        }
    }

    // ── Setup ViewPager2 tabs ─────────────────────────────

    private void setupTabs() {
        final String[] TABS = { "Attendance", "Activities", "Info" };
        final int finalChildId = childId;

        binding.viewPager.setAdapter(new FragmentStateAdapter(this) {
            @Override public int getItemCount() { return 3; }

            @Override
            public Fragment createFragment(int position) {
                switch (position) {
                    case 0: return AttendanceTabFragment.newInstance(finalChildId);
                    case 1: return ActivitiesFragment.newInstance(finalChildId);
                    case 2: return InfoFragment.newInstance(childData);
                    default: return AttendanceTabFragment.newInstance(finalChildId);
                }
            }
        });

        new TabLayoutMediator(binding.tabLayout, binding.viewPager,
            (tab, pos) -> tab.setText(TABS[pos])
        ).attach();
    }

    // ── Helpers ───────────────────────────────────────────

    private void showNoChildMessage() {
        Toast.makeText(this,
            "No children linked to your account. Contact the learning center.",
            Toast.LENGTH_LONG).show();
        finish();
    }

    private void sessionExpired() {
        session.clearSession();
        Intent i = new Intent(this, LoginActivity.class);
        i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        startActivity(i);
    }

    private String capitalize(String s) {
        if (s == null || s.isEmpty()) return "—";
        return Character.toUpperCase(s.charAt(0)) + s.substring(1);
    }
}
