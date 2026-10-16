import 'package:chewie/chewie.dart';
import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/player/providers/player_provider.dart';
import 'package:provider/provider.dart';
import 'package:video_player/video_player.dart';

class PlayerView extends StatelessWidget {
  const PlayerView({super.key});

  @override
  Widget build(BuildContext context) {
    final player = context.watch<PlayerProvider>();
    return Scaffold(
      backgroundColor: Colors.black,
      body: player.loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : player.error != null
              ? StateMessage(title: 'Playback failed', subtitle: player.error, onRetry: player.open)
              : _Stage(player: player),
    );
  }
}

class _Stage extends StatelessWidget {
  const _Stage({required this.player});

  final PlayerProvider player;

  @override
  Widget build(BuildContext context) {
    final controller = player.controller!;
    return GestureDetector(
      behavior: HitTestBehavior.opaque,
      onTapDown: (details) {
        final box = context.findRenderObject() as RenderBox?;
        final width = box?.size.width ?? MediaQuery.sizeOf(context).width;
        player.rememberTap(details.localPosition.dx > width / 2);
      },
      onTap: player.onSurfaceTap,
      onDoubleTap: player.seekFromLastTap,
      child: Stack(
        fit: StackFit.expand,
        children: [
          if (!player.pip) Center(child: player.chewie == null ? VideoPlayer(controller) : Chewie(controller: player.chewie!)),
          if (player.buffering) const Center(child: CircularProgressIndicator(color: ColorConstant.white)),
          if (player.seekHint != null) _SeekFlash(forward: player.seekForward, label: player.seekHint!),
          if (!player.showControls && !player.locked)
            Align(
              alignment: Alignment.bottomCenter,
              child: LinearProgressIndicator(
                minHeight: 3,
                value: player.durationSeconds == 0 ? 0 : (player.positionSeconds / player.durationSeconds).clamp(0, 1),
                backgroundColor: Colors.white24,
                color: ColorConstant.primaryCl,
              ),
            ),
          if (player.pip)
            _PipWindow(player: player)
          else ...[
            if (!player.playing && !player.showControls && !player.locked)
              const IgnorePointer(
                child: Center(
                  child: DecoratedBox(
                    decoration: BoxDecoration(color: ColorConstant.primaryCl, borderRadius: BorderRadius.all(Radius.circular(14))),
                    child: SizedBox(
                      width: 68,
                      height: 48,
                      child: Icon(Icons.play_arrow_rounded, color: ColorConstant.white, size: 34),
                    ),
                  ),
                ),
              ),
            if (player.showControls && !player.locked) _Controls(player: player),
          ],
          if (player.locked && player.showLockButton)
            Align(
              alignment: Alignment.centerRight,
              child: Padding(
                padding: const EdgeInsets.only(right: 20),
                child: _RoundButton(icon: Icons.lock_open_rounded, onTap: player.toggleLock),
              ),
            ),
          if (player.menuOpen) _MoreSheet(player: player),
          if (player.speedOpen) _SpeedSheet(player: player),
        ],
      ),
    );
  }
}

class _Controls extends StatelessWidget {
  const _Controls({required this.player});

  final PlayerProvider player;

  @override
  Widget build(BuildContext context) {
    final max = player.durationSeconds <= 0 ? 1.0 : player.durationSeconds;
    return Stack(
      fit: StackFit.expand,
      children: [
        const DecoratedBox(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [Color(0xCC000000), Color(0x00000000), Color(0x00000000), Color(0xE6000000)],
              stops: [0, 0.28, 0.62, 1],
            ),
          ),
        ),
        SafeArea(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(8, 4, 12, 8),
            child: Column(
              children: [
                Row(
                  children: [
                    IconButton(
                      onPressed: () => Navigator.maybePop(context),
                      icon: const Icon(Icons.arrow_back_ios_new_rounded, color: ColorConstant.white, size: 20),
                    ),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            player.video.title,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.semiBold, fontSize: 16),
                          ),
                          if ((player.video.categoryName ?? '').isNotEmpty)
                            Text(
                              player.video.categoryName!,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(color: ColorConstant.lightGrayIcCl, fontSize: 12),
                            ),
                        ],
                      ),
                    ),
                    _RoundButton(icon: Icons.more_vert_rounded, onTap: player.toggleMenu),
                    const SizedBox(width: 8),
                    _RoundButton(icon: Icons.lock_outline_rounded, onTap: player.toggleLock),
                  ],
                ),
                const Spacer(),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    _RoundButton(icon: Icons.replay_10_rounded, onTap: () => player.seekBy(-10)),
                    const SizedBox(width: 28),
                    _RoundButton(
                      icon: player.ended ? Icons.replay_rounded : (player.playing ? Icons.pause_rounded : Icons.play_arrow_rounded),
                      large: true,
                      onTap: player.togglePlay,
                    ),
                    const SizedBox(width: 28),
                    _RoundButton(icon: Icons.forward_10_rounded, onTap: () => player.seekBy(10)),
                  ],
                ),
                const Spacer(),
                Row(
                  children: [
                    Text(player.clock(player.positionSeconds), style: const TextStyle(color: ColorConstant.white, fontSize: 12, fontFamily: FontsStyle.medium)),
                    Expanded(
                      child: SliderTheme(
                        data: SliderTheme.of(context).copyWith(
                          trackHeight: 3,
                          thumbShape: const RoundSliderThumbShape(enabledThumbRadius: 7),
                          overlayShape: const RoundSliderOverlayShape(overlayRadius: 14),
                          activeTrackColor: ColorConstant.primaryCl,
                          inactiveTrackColor: Colors.white24,
                          thumbColor: ColorConstant.white,
                        ),
                        child: Slider(
                          min: 0,
                          max: max,
                          value: player.positionSeconds.clamp(0, max).toDouble(),
                          onChangeStart: player.beginSeek,
                          onChanged: player.updateSeek,
                          onChangeEnd: player.endSeek,
                        ),
                      ),
                    ),
                    Text(player.clock(player.durationSeconds), style: const TextStyle(color: ColorConstant.white, fontSize: 12, fontFamily: FontsStyle.medium)),
                  ],
                ),
                Row(
                  children: [
                    const SizedBox(width: 8),
                    _Pill(label: player.speedLabel, onTap: player.toggleSpeedSheet),
                    IconButton(
                      onPressed: player.toggleMute,
                      icon: Icon(
                        player.muted ? Icons.volume_off_rounded : Icons.volume_up_rounded,
                        color: ColorConstant.white,
                      ),
                    ),
                    const Spacer(),
                    IconButton(
                      onPressed: player.toggleFullscreen,
                      icon: Icon(
                        player.fullscreen ? Icons.fullscreen_exit_rounded : Icons.fullscreen_rounded,
                        color: ColorConstant.white,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}

class _MoreSheet extends StatelessWidget {
  const _MoreSheet({required this.player});

  final PlayerProvider player;

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.bottomCenter,
      child: Container(
        margin: const EdgeInsets.all(16),
        decoration: const BoxDecoration(
          color: ColorConstant.surfaceCl,
          borderRadius: BorderRadius.vertical(top: Radius.circular(16), bottom: Radius.circular(16)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(
              leading: const Icon(Icons.speed_rounded, color: ColorConstant.darkGrayCl),
              title: const Text('Playback speed', style: TextStyle(fontFamily: FontsStyle.medium)),
              trailing: Text(player.speed == 1 ? 'Normal' : player.speedLabel, style: const TextStyle(fontFamily: FontsStyle.medium)),
              onTap: player.toggleSpeedSheet,
            ),
            ListTile(
              leading: const Icon(Icons.picture_in_picture_alt_rounded, color: ColorConstant.darkGrayCl),
              title: const Text('Picture in picture', style: TextStyle(fontFamily: FontsStyle.medium)),
              onTap: player.enterPip,
            ),
          ],
        ),
      ),
    );
  }
}

class _PipWindow extends StatelessWidget {
  const _PipWindow({required this.player});

  final PlayerProvider player;

  @override
  Widget build(BuildContext context) {
    final controller = player.controller!;
    final aspect = controller.value.aspectRatio == 0 ? 16 / 9 : controller.value.aspectRatio;
    return Stack(
      children: [
        Positioned(
          left: player.pipOffset.dx,
          top: player.pipOffset.dy,
          child: GestureDetector(
            onPanUpdate: (details) => player.movePip(details.delta),
            child: Material(
              color: Colors.black,
              elevation: 10,
              borderRadius: BorderRadius.circular(10),
              clipBehavior: Clip.antiAlias,
              child: SizedBox(
                width: 220,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      color: const Color(0xFF202124),
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
                      child: Row(
                        children: [
                          const Icon(Icons.smart_display_outlined, color: Colors.white70, size: 14),
                          const SizedBox(width: 6),
                          Expanded(
                            child: Text(
                              player.video.title,
                              style: const TextStyle(color: Colors.white70, fontSize: 11),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          InkWell(
                            onTap: player.exitPip,
                            child: const Icon(Icons.close_rounded, color: Colors.white70, size: 16),
                          ),
                        ],
                      ),
                    ),
                    AspectRatio(
                      aspectRatio: aspect,
                      child: Stack(
                        fit: StackFit.expand,
                        children: [
                          VideoPlayer(controller),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                            children: [
                              _pipIcon(Icons.replay_10_rounded, () => player.seekBy(-10)),
                              _pipIcon(
                                player.playing ? Icons.pause_rounded : Icons.play_arrow_rounded,
                                player.togglePlay,
                                size: 30,
                              ),
                              _pipIcon(Icons.forward_10_rounded, () => player.seekBy(10)),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _pipIcon(IconData icon, VoidCallback onTap, {double size = 22}) {
    return InkWell(
      onTap: onTap,
      customBorder: const CircleBorder(),
      child: Padding(
        padding: const EdgeInsets.all(6),
        child: Icon(icon, color: Colors.white, size: size),
      ),
    );
  }
}

class _SpeedSheet extends StatelessWidget {
  const _SpeedSheet({required this.player});

  final PlayerProvider player;

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.bottomCenter,
      child: Container(
        margin: const EdgeInsets.all(16),
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 8),
        decoration: BoxDecoration(color: ColorConstant.surfaceCl, borderRadius: BorderRadius.circular(16)),
        child: Wrap(
          alignment: WrapAlignment.center,
          children: PlayerProvider.speeds.map((speed) {
            final selected = player.speed == speed;
            final label = speed == 1 ? 'Normal' : (speed == speed.roundToDouble() ? '${speed.toInt()}x' : '${speed}x');
            return InkWell(
              onTap: () => player.setSpeed(speed),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                decoration: BoxDecoration(
                  color: selected ? ColorConstant.primaryCl : Colors.transparent,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(label, style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.semiBold)),
              ),
            );
          }).toList(),
        ),
      ),
    );
  }
}

class _SeekFlash extends StatelessWidget {
  const _SeekFlash({required this.forward, required this.label});

  final bool forward;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: forward ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        width: 120,
        margin: const EdgeInsets.symmetric(horizontal: 24),
        padding: const EdgeInsets.symmetric(vertical: 18),
        decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(16)),
        child: Text(
          label,
          textAlign: TextAlign.center,
          style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.bold, fontSize: 18),
        ),
      ),
    );
  }
}

class _RoundButton extends StatelessWidget {
  const _RoundButton({required this.icon, required this.onTap, this.large = false});

  final IconData icon;
  final VoidCallback onTap;
  final bool large;

  @override
  Widget build(BuildContext context) {
    final size = large ? 64.0 : 40.0;
    return Material(
      color: large ? ColorConstant.primaryCl : Colors.black45,
      shape: large ? RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)) : const CircleBorder(),
      child: InkWell(
        customBorder: large ? RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)) : const CircleBorder(),
        onTap: onTap,
        child: SizedBox(
          width: large ? 72 : size,
          height: large ? 52 : size,
          child: Icon(icon, color: ColorConstant.white, size: large ? 34 : 22),
        ),
      ),
    );
  }
}

class _Pill extends StatelessWidget {
  const _Pill({required this.label, required this.onTap});

  final String label;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.white54),
        ),
        child: Text(label, style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.semiBold, fontSize: 13)),
      ),
    );
  }
}
