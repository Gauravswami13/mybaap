import 'dart:async';

import 'package:chewie/chewie.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mybaap/modules/home/models/catalog_models.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:video_player/video_player.dart';

class PlayerProvider extends ChangeNotifier {
  PlayerProvider(this.api, this.video);

  final ApiClient api;
  final VideoItem video;

  static const speeds = [0.25, 0.5, 0.75, 1.0, 1.25, 1.5, 1.75, 2.0];

  VideoPlayerController? controller;
  ChewieController? chewie;
  bool loading = true;
  String? error;
  bool showControls = true;
  bool locked = false;
  bool showLockButton = false;
  bool speedOpen = false;
  bool menuOpen = false;
  bool pip = false;
  Offset pipOffset = const Offset(16, 100);
  bool fullscreen = false;
  bool seeking = false;
  double dragSeconds = 0;
  double speed = 1;
  bool muted = false;
  String? seekHint;
  bool seekForward = true;
  bool _lastTapForward = true;

  bool _disposed = false;
  Timer? _hideTimer;
  Timer? _hintTimer;

  Future<void> open() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final data = await api.get('/videos/${video.id}/playback');
      if (_disposed) return;
      if (data is! Map || data['url'] is! String) {
        throw ApiException('Playback URL was not returned.');
      }
      final next = VideoPlayerController.networkUrl(Uri.parse(data['url'] as String));
      await next.initialize();
      if (_disposed) {
        await next.dispose();
        return;
      }
      controller = next;
      chewie = ChewieController(
        videoPlayerController: next,
        autoPlay: true,
        looping: false,
        showControls: false,
        allowFullScreen: false,
        allowMuting: false,
        showOptions: false,
        allowedScreenSleep: false,
      );
      next.addListener(_onTick);
      await next.play();
      loading = false;
      showControls = true;
      notifyListeners();
      _armHide();
      await SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);
    } on ApiException catch (exception) {
      if (_disposed) return;
      loading = false;
      error = exception.message;
      notifyListeners();
    } catch (_) {
      if (_disposed) return;
      loading = false;
      error = 'This sample file cannot be played. Upload a real video from the admin panel.';
      notifyListeners();
    }
  }

  void _onTick() {
    if (_disposed || seeking) return;
    notifyListeners();
  }

  void rememberTap(bool forward) {
    _lastTapForward = forward;
  }

  void seekFromLastTap() {
    seekBy(_lastTapForward ? 10 : -10);
  }

  void onSurfaceTap() {
    if (locked) {
      showLockButton = !showLockButton;
      notifyListeners();
      return;
    }
    if (showControls) {
      showControls = false;
      speedOpen = false;
      menuOpen = false;
      _hideTimer?.cancel();
      notifyListeners();
      return;
    }
    reveal();
  }

  void reveal() {
    if (locked) return;
    showControls = true;
    notifyListeners();
    _armHide();
  }

  void _armHide() {
    _hideTimer?.cancel();
    if (controller?.value.isPlaying != true) return;
    _hideTimer = Timer(const Duration(seconds: 3), () {
      if (_disposed || locked || seeking) return;
      showControls = false;
      speedOpen = false;
      menuOpen = false;
      notifyListeners();
    });
  }

  Future<void> togglePlay() async {
    final player = controller;
    if (player == null) return;
    final value = player.value;
    if (value.isPlaying) {
      await player.pause();
      showControls = true;
      _hideTimer?.cancel();
    } else {
      if (value.duration > Duration.zero && value.position >= value.duration) {
        await player.seekTo(Duration.zero);
      }
      await player.play();
      reveal();
    }
    notifyListeners();
  }

  Future<void> seekBy(int seconds) async {
    final player = controller;
    if (player == null || locked) return;
    seekForward = seconds > 0;
    var next = player.value.position + Duration(seconds: seconds);
    if (next < Duration.zero) next = Duration.zero;
    if (next > player.value.duration) next = player.value.duration;
    await player.seekTo(next);
    seekHint = seconds > 0 ? '+${seconds}s' : '${seconds}s';
    notifyListeners();
    _hintTimer?.cancel();
    _hintTimer = Timer(const Duration(milliseconds: 700), () {
      seekHint = null;
      if (!_disposed) notifyListeners();
    });
    reveal();
  }

  void beginSeek(double seconds) {
    seeking = true;
    dragSeconds = seconds;
    _hideTimer?.cancel();
    notifyListeners();
  }

  void updateSeek(double seconds) {
    dragSeconds = seconds;
    notifyListeners();
  }

  Future<void> endSeek(double seconds) async {
    seeking = false;
    dragSeconds = seconds;
    await controller?.seekTo(Duration(milliseconds: (seconds * 1000).round()));
    reveal();
  }

  Future<void> toggleMute() async {
    muted = !muted;
    await controller?.setVolume(muted ? 0 : 1);
    notifyListeners();
    reveal();
  }

  Future<void> setSpeed(double value) async {
    speed = value;
    speedOpen = false;
    menuOpen = false;
    await controller?.setPlaybackSpeed(value);
    notifyListeners();
    reveal();
  }

  void toggleSpeedSheet() {
    speedOpen = !speedOpen;
    menuOpen = false;
    if (speedOpen) _hideTimer?.cancel();
    notifyListeners();
  }

  void toggleMenu() {
    menuOpen = !menuOpen;
    speedOpen = false;
    if (menuOpen) _hideTimer?.cancel();
    notifyListeners();
  }

  void enterPip() {
    pip = true;
    menuOpen = false;
    speedOpen = false;
    showControls = false;
    controller?.play();
    notifyListeners();
  }

  void exitPip() {
    pip = false;
    reveal();
  }

  void movePip(Offset delta) {
    pipOffset += delta;
    notifyListeners();
  }

  void toggleLock() {
    locked = !locked;
    showLockButton = locked;
    speedOpen = false;
    menuOpen = false;
    if (locked) {
      showControls = false;
      _hideTimer?.cancel();
    } else {
      reveal();
    }
    notifyListeners();
  }

  Future<void> toggleFullscreen() async {
    fullscreen = !fullscreen;
    if (fullscreen) {
      await SystemChrome.setPreferredOrientations([
        DeviceOrientation.landscapeLeft,
        DeviceOrientation.landscapeRight,
      ]);
    } else {
      await SystemChrome.setPreferredOrientations([
        DeviceOrientation.portraitUp,
        DeviceOrientation.portraitDown,
      ]);
    }
    notifyListeners();
    reveal();
  }

  double get positionSeconds {
    if (seeking) return dragSeconds;
    final value = controller?.value.position.inMilliseconds ?? 0;
    return value / 1000;
  }

  double get durationSeconds {
    final value = controller?.value.duration.inMilliseconds ?? 0;
    return value / 1000;
  }

  bool get playing => controller?.value.isPlaying ?? false;
  bool get buffering => controller?.value.isBuffering ?? false;
  bool get ended {
    final value = controller?.value;
    if (value == null || value.duration == Duration.zero) return false;
    return !value.isPlaying && value.position >= value.duration;
  }

  String clock(double seconds) {
    final total = seconds.round().clamp(0, 86400);
    final hours = total ~/ 3600;
    final minutes = (total % 3600) ~/ 60;
    final remain = total % 60;
    final mm = minutes.toString().padLeft(2, '0');
    final ss = remain.toString().padLeft(2, '0');
    if (hours > 0) return '$hours:$mm:$ss';
    return '$mm:$ss';
  }

  String get speedLabel {
    if (speed == speed.roundToDouble()) return '${speed.toInt()}x';
    return '${speed}x';
  }

  @override
  void dispose() {
    _disposed = true;
    _hideTimer?.cancel();
    _hintTimer?.cancel();
    controller?.removeListener(_onTick);
    chewie?.dispose();
    controller?.dispose();
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
    SystemChrome.setPreferredOrientations([
      DeviceOrientation.portraitUp,
      DeviceOrientation.portraitDown,
    ]);
    super.dispose();
  }
}
