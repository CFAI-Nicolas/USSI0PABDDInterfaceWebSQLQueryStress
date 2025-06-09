using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using System.Data.SqlClient;
using System.Diagnostics;

public class SqlQueryModel : PageModel
{
    [BindProperty] public string SqlText { get; set; } = string.Empty;
    [BindProperty] public int ExecutionCount { get; set; } = 1;
    [BindProperty] public int DelayMs { get; set; } = 0;
    [BindProperty] public bool ShowResults { get; set; } = true;

    public List<Dictionary<string, object>>? QueryResults { get; set; }
    public double AverageDurationMs { get; set; }
    public long TotalDurationMs { get; set; }
    public string? ErrorMessage { get; set; }

    public void OnGet()
    {
    }

    public async Task<IActionResult> OnPostAsync()
    {
        var results = new List<Dictionary<string, object>>();
        var executionTimes = new List<long>();
        Stopwatch totalSw = Stopwatch.StartNew();

        string? connectionString = HttpContext.Session.GetString("ConnectionString");

        if (string.IsNullOrEmpty(connectionString))
        {
            ErrorMessage = "Aucune connexion active. Veuillez vous reconnecter.";
            return RedirectToPage("/Index");
        }

        try
        {
            using var connection = new SqlConnection(connectionString);
            await connection.OpenAsync();

            for (int i = 0; i < ExecutionCount; i++)
            {
                Stopwatch sw = Stopwatch.StartNew();

                using var command = new SqlCommand(SqlText, connection);

                if (ShowResults)
                {
                    using var reader = await command.ExecuteReaderAsync();
                    if (i == 0) // On récupère les résultats une seule fois
                    {
                        while (await reader.ReadAsync())
                        {
                            var row = new Dictionary<string, object>();
                            for (int j = 0; j < reader.FieldCount; j++)
                            {
                                row[reader.GetName(j)] = reader.GetValue(j);
                            }
                            results.Add(row);
                        }
                    }
                }
                else
                {
                    await command.ExecuteNonQueryAsync();
                }

                sw.Stop();
                executionTimes.Add(sw.ElapsedMilliseconds);

                if (DelayMs > 0)
                    await Task.Delay(DelayMs);
            }

            totalSw.Stop();
            TotalDurationMs = totalSw.ElapsedMilliseconds;
            AverageDurationMs = executionTimes.Average();
            QueryResults = results;
        }
        catch (Exception ex)
        {
            ErrorMessage = ex.Message;
        }

        return Page();
    }
}
