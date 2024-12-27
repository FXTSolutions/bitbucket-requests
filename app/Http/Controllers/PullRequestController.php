<?php

namespace App\Http\Controllers;

use App\Models\PullRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class PullRequestController extends Controller
{
    const DATE_TIME_FORMAT = 'd/m/Y H:i:s';

    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        return view('pull_requests.index');
    }

    public function show(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
    {
        $branch = $request->input('branch', 'master');
        $showMergedDetails = $request->has('show_merged_details');

        $pullRequests = $this->getPullRequests();

        $masterPRs = array_filter($pullRequests, function ($pr) {
            return $pr['destination']['branch']['name'] === 'master';
        });

        $formattedData = [];
        foreach ($masterPRs as $pr) {
            $branchOrigin = $pr['source']['branch']['name'];

            $serverDevData = $this->getBranchStatus($pullRequests, $pr, $branchOrigin, 'server-dev', $showMergedDetails);
            $serverDevStatus = $serverDevData['status'];
            $serverDevStatusColor = $serverDevData['statusColor'];

            $serverQaData = $this->getBranchStatus($pullRequests, $pr, $branchOrigin, 'server-qa', $showMergedDetails);
            $serverQaStatus = $serverQaData['status'];
            $serverQaStatusColor = $serverQaData['statusColor'];

            $formattedData[] = [
                'branch_origin' => $branchOrigin,
                'title' => $pr['title'],
                'server_dev_status' => $serverDevStatus,
                'server_dev_status_color' => $serverDevStatusColor,
                'server_qa_status' => $serverQaStatus,
                'server_qa_status_color' => $serverQaStatusColor,
                'author' => $pr['author']['display_name'],
                'created_on' => Carbon::parse($pr['created_on'])->format(self::DATE_TIME_FORMAT),
                'updated_on' => Carbon::parse($pr['updated_on'])->format(self::DATE_TIME_FORMAT),
            ];
        }

        return view('pull_requests.show', compact('formattedData'));
    }

    private function getBranchStatus(array $pullRequests, array $pr, string $branchOrigin, string $destinationBranch, bool $showMergedDetails): array
    {
        $status = 'Pendente';
        $statusColor = '';

        $prForBranch = $this->findPullRequest($pullRequests, $branchOrigin, $destinationBranch);

        if (!$prForBranch) {
            if (!$showMergedDetails) {
                return ['status' => '', 'statusColor' => 'bg-green-500'];
            }

            $dateMerged = $this->getLastMergeDate($destinationBranch, $branchOrigin);

            if ($dateMerged) {
                $status = Carbon::parse($dateMerged)->format(self::DATE_TIME_FORMAT);

                if (Carbon::parse($dateMerged)->lessThan(Carbon::parse($pr['updated_on']))) {
                    $statusColor = 'bg-red-500';
                } else {
                    $statusColor = 'bg-green-500';
                }
            }
        }

        return ['status' => $status, 'statusColor' => $statusColor];
    }

    private function getPullRequests(): \Illuminate\Http\JsonResponse|array
    {
        $workspace = env('BITBUCKET_WORKSPACE');
        $repoSlug = env('BITBUCKET_REPO_SLUG');
        $username = env('BITBUCKET_USERNAME');
        $appPassword = env('BITBUCKET_APP_PASSWORD');

        $url = "https://api.bitbucket.org/2.0/repositories/{$workspace}/{$repoSlug}/pullrequests";

        $pullRequests = [];
        $nextPage = $url;

        while ($nextPage) {
            $response = Http::withBasicAuth($username, $appPassword)->get($nextPage);

            if ($response->failed()) {
                return response()->json(['error' => 'Erro ao conectar com a API do Bitbucket'], 500);
            }

            $data = $response->json();
            if (isset($data['values'])) {
                $pullRequests = array_merge($pullRequests, $data['values']);
            }
            $nextPage = $data['next'] ?? null;
        }

        return $pullRequests;
    }

    private function findPullRequest(array $pullRequests, string $branchOrigin, string $destinationBranch)
    {
        foreach ($pullRequests as $pr) {
            if (
                $pr['source']['branch']['name'] === $branchOrigin &&
                $pr['destination']['branch']['name'] === $destinationBranch
            ) {
                return $pr;
            }
        }

        return null;
    }

    private function getLastMergeDate(string $branchName, string $branchOrigin)
    {
        $workspace = env('BITBUCKET_WORKSPACE');
        $repoSlug = env('BITBUCKET_REPO_SLUG');
        $username = env('BITBUCKET_USERNAME');
        $appPassword = env('BITBUCKET_APP_PASSWORD');

        $url = "https://api.bitbucket.org/2.0/repositories/{$workspace}/{$repoSlug}/pullrequests?state=MERGED&source.branch.name={$branchOrigin}&destination.branch.name={$branchName}";

        $response = Http::withBasicAuth($username, $appPassword)->get($url);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();
        if (isset($data['values']) && count($data['values']) > 0) {
            return $data['values'][0]['updated_on'];
        }

        return null;
    }

    public function updateObservations(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        foreach ($request->input('observations') as $branchOrigin => $observation) {
            $pullRequest = PullRequest::where('branch_origin', $branchOrigin)->first();
            if ($pullRequest) {
                $pullRequest->observation = $observation;
                $pullRequest->save();
            }
        }

        return redirect('/pull-requests')->with('status', 'Observações atualizadas com sucesso!');
    }
}
