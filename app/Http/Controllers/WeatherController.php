<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;


class WeatherController extends Controller
{
    public function index()
    {
        return view('weather.index');
    }

    public function search(Request $request)
    {
        $city = $request->query('city');

        $apiKey = env('WEATHER_API_KEY');
        $url = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric";

        $response = Http::get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'City not found'], 404);
        }

        return $response->json();
    }

    public function forecast(Request $request)
    {
        $city = $request->query('city');
        $apiKey = env('WEATHER_API_KEY');
        $url = "https://api.openweathermap.org/data/2.5/forecast?q=$city&appid=$apiKey&units=metric";

        $response = Http::get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'City not found'], 404);
        }

        return $response->json();
    }

    public function aiRecommendation(Request $request)
    {
        $weather = $request->input('weather'); // contoh: "rain", "sunny", "clouds"

        $prompt = "Buat rekomendasi singkat berdasarkan cuaca: $weather";

        $response = OpenAI::completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 50,
        ]);

        return response()->json([
            'recommendation' => $response->choices[0]->text
        ]);
    }

}
