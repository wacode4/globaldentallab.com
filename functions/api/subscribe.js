function buildHeaders() {
  return {
    "Access-Control-Allow-Origin": "*",
    "Cache-Control": "no-store",
    "Content-Type": "application/json",
  };
}

function normalizeText(value, maxLength) {
  const stringValue = typeof value === "string" ? value.trim() : "";

  if (!stringValue) {
    return "";
  }

  return stringValue.slice(0, maxLength);
}

export async function onRequestPost(context) {
  const { request, env } = context;
  const headers = buildHeaders();

  try {
    const data = await request.json();
    const email = normalizeText(data.email, 160).toLowerCase();
    const website = normalizeText(data.website, 255);

    if (website) {
      return new Response(
        JSON.stringify({ success: true, message: "Thank you for subscribing!" }),
        { status: 200, headers },
      );
    }

    if (!email) {
      return new Response(
        JSON.stringify({ success: false, error: "Email is required" }),
        { status: 400, headers },
      );
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return new Response(
        JSON.stringify({ success: false, error: "Invalid email format" }),
        { status: 400, headers },
      );
    }

    if (!env.DB) {
      return new Response(
        JSON.stringify({ success: false, error: "Database is not configured" }),
        { status: 500, headers },
      );
    }

    const existing = await env.DB.prepare(
      "SELECT id, status FROM subscriptions WHERE email = ?",
    )
      .bind(email)
      .first();

    if (existing) {
      if (existing.status === "active") {
        return new Response(
          JSON.stringify({
            success: true,
            message: "You are already subscribed.",
          }),
          { status: 200, headers },
        );
      }

      await env.DB.prepare(
        "UPDATE subscriptions SET status = ? WHERE email = ?",
      )
        .bind("active", email)
        .run();

      return new Response(
        JSON.stringify({
          success: true,
          message: "Welcome back! Your subscription has been reactivated.",
        }),
        { status: 200, headers },
      );
    }

    await env.DB.prepare("INSERT INTO subscriptions (email) VALUES (?)")
      .bind(email)
      .run();

    return new Response(
      JSON.stringify({ success: true, message: "Thank you for subscribing!" }),
      { status: 200, headers },
    );
  } catch (error) {
    console.error("Subscribe error:", error);
    return new Response(
      JSON.stringify({
        success: false,
        error: "Server error. Please try again.",
      }),
      { status: 500, headers },
    );
  }
}

export async function onRequestOptions() {
  return new Response(null, {
    headers: {
      ...buildHeaders(),
      "Access-Control-Allow-Methods": "POST, OPTIONS",
      "Access-Control-Allow-Headers": "Content-Type",
    },
  });
}
