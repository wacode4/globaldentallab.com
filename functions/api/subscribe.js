export async function onRequestPost(context) {
  const { request, env } = context;

  const headers = {
    "Access-Control-Allow-Origin": "*",
    "Content-Type": "application/json",
  };

  try {
    const data = await request.json();
    const { email } = data;

    if (!email) {
      return new Response(
        JSON.stringify({ success: false, error: "Email is required" }),
        { status: 400, headers },
      );
    }

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return new Response(
        JSON.stringify({ success: false, error: "Invalid email format" }),
        { status: 400, headers },
      );
    }

    // Check if already subscribed
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
      } else {
        // Reactivate subscription
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
    }

    // Insert new subscription
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
      "Access-Control-Allow-Origin": "*",
      "Access-Control-Allow-Methods": "POST, OPTIONS",
      "Access-Control-Allow-Headers": "Content-Type",
    },
  });
}
