package com.alamada.childmonitor.fragments;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.alamada.childmonitor.R;
import com.alamada.childmonitor.adapters.ActivityAdapter;
import com.alamada.childmonitor.models.ActivityListResponse;
import com.alamada.childmonitor.models.ActivityRecord;
import com.alamada.childmonitor.network.ApiClient;
import com.alamada.childmonitor.utils.SessionManager;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ActivitiesFragment extends Fragment {

    private static final String ARG_CHILD_ID = "child_id";

    private int childId;
    private SessionManager session;
    private ActivityAdapter adapter;
    private final List<ActivityRecord> activities = new ArrayList<>();

    public static ActivitiesFragment newInstance(int childId) {
        ActivitiesFragment f = new ActivitiesFragment();
        Bundle args = new Bundle();
        args.putInt(ARG_CHILD_ID, childId);
        f.setArguments(args);
        return f;
    }

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) childId = getArguments().getInt(ARG_CHILD_ID);
        session = new SessionManager(requireContext());
    }

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater,
                             @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_tab_activities, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        RecyclerView rv        = view.findViewById(R.id.rv_activities);
        View emptyView         = view.findViewById(R.id.tv_empty);
        ProgressBar progress   = view.findViewById(R.id.progress_bar);
        SwipeRefreshLayout swipe = view.findViewById(R.id.swipe_refresh);

        adapter = new ActivityAdapter(activities);
        rv.setLayoutManager(new LinearLayoutManager(requireContext()));
        rv.setAdapter(adapter);

        swipe.setColorSchemeResources(R.color.primary);
        swipe.setOnRefreshListener(() -> loadActivities(emptyView, progress, swipe));

        progress.setVisibility(View.VISIBLE);
        loadActivities(emptyView, progress, swipe);
    }

    private void loadActivities(View emptyView, ProgressBar progress,
                                 SwipeRefreshLayout swipe) {
        ApiClient.getInstance(session.getBearerToken())
            .getActivities(childId)
            .enqueue(new Callback<ActivityListResponse>() {
                @Override
                public void onResponse(Call<ActivityListResponse> call,
                                       Response<ActivityListResponse> response) {
                    progress.setVisibility(View.GONE);
                    swipe.setRefreshing(false);

                    if (response.isSuccessful() && response.body() != null) {
                        activities.clear();
                        if (response.body().data != null)
                            activities.addAll(response.body().data);
                        adapter.notifyDataSetChanged();
                        emptyView.setVisibility(activities.isEmpty() ? View.VISIBLE : View.GONE);
                    }
                }
                @Override
                public void onFailure(Call<ActivityListResponse> call, Throwable t) {
                    progress.setVisibility(View.GONE);
                    swipe.setRefreshing(false);
                }
            });
    }
}
