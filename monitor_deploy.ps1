$ak   = "rnd_fmSAk65nhK8lllLcJPjmvrTkDtc9"
$sid  = "srv-dbevq4on74is73b2cnqg"
$did  = "dep-dbf3fa04cbqc73dqa500"
$hdrs = @{"Authorization"="Bearer $ak"}
$url  = "https://api.render.com/v1/services/$sid/deploys/$did"

$s = "queued"
while ($s -notin @("live","build_failed","update_failed","canceled","pre_deploy_failed")) {
    Start-Sleep -Seconds 30
    $p = Invoke-WebRequest $url -Headers $hdrs -UseBasicParsing
    $s = ($p.Content | ConvertFrom-Json).status
    Write-Host "$(Get-Date -Format 'HH:mm:ss') $s"
}

if ($s -eq "live") {
    Write-Host "SUCCESS! https://alamada-child-monitoring-system.onrender.com/api/health"
} else {
    Write-Host "FAILED: $s"
}
