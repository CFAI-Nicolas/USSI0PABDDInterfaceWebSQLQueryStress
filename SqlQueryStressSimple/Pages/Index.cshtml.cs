using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using System.Data.SqlClient;

public class IndexModel : PageModel
{
[BindProperty] public string Server { get; set; } = string.Empty;
[BindProperty] public string Database { get; set; } = string.Empty;
[BindProperty] public string Username { get; set; } = string.Empty;
[BindProperty] public string Password { get; set; } = string.Empty;
public string Message { get; set; } = string.Empty;

    public bool IsSuccess { get; set; }

    public void OnGet()
    {
        // initialisation si besoin
    }

  public IActionResult OnPost()
{
    string connectionString;

    if (string.IsNullOrWhiteSpace(Username) && string.IsNullOrWhiteSpace(Password))
    {
        connectionString = $"Server={Server};Database={Database};Integrated Security=true;TrustServerCertificate=true;";
    }
    else
    {
        connectionString = $"Server={Server};Database={Database};User Id={Username};Password={Password};TrustServerCertificate=true;";
    }

    try
    {
        using var connection = new SqlConnection(connectionString);
        connection.Open();

        // Sauvegarder la chaîne de connexion temporairement (session)
        HttpContext.Session.SetString("ConnectionString", connectionString);

        return RedirectToPage("/SqlQuery");
    }
    catch (Exception ex)
    {
        IsSuccess = false;
        Message = $"Erreur : {ex.Message}";
        return Page();
    }
}

}
