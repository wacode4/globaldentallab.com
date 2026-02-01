export async function onRequestGet(context) {
  const { request, env } = context;

  const headers = {
    "Access-Control-Allow-Origin": "*",
    "Content-Type": "application/json",
  };

  // Simple auth check
  const url = new URL(request.url);
  const key = url.searchParams.get("key");
  if (key !== "gdl2026") {
    return new Response(JSON.stringify({ error: "Unauthorized" }), {
      status: 401,
      headers,
    });
  }

  try {
    const results = await env.DB.prepare(
      "SELECT * FROM subscriptions ORDER BY created_at DESC",
    ).all();

    return new Response(JSON.stringify(results.results), {
      status: 200,
      headers,
    });
  } catch (error) {
    return new Response(JSON.stringify({ error: error.message }), {
      status: 500,
      headers,
    });
  }
}
