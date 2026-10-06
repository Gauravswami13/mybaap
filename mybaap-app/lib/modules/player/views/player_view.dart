import 'package:chewie/chewie.dart';
import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/modules/home/models/catalog_models.dart';
import 'package:mybaap/modules/home/providers/home_provider.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:provider/provider.dart';
import 'package:video_player/video_player.dart';

class PlayerView extends StatefulWidget {
  const PlayerView({super.key, required this.video});

  final VideoItem video;

  @override
  State<PlayerView> createState() => _PlayerViewState();
}

class _PlayerViewState extends State<PlayerView> {
  VideoPlayerController? _video;
  ChewieController? _chewie;
  String? _error;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _open();
  }

  Future<void> _open() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final url = await context.read<HomeProvider>().playbackUrl(widget.video.id);
      final controller = VideoPlayerController.networkUrl(Uri.parse(url));
      await controller.initialize();
      if (!mounted) {
        await controller.dispose();
        return;
      }
      _video = controller;
      _chewie = ChewieController(
        videoPlayerController: controller,
        autoPlay: true,
        looping: false,
      );
      setState(() => _loading = false);
    } on ApiException catch (error) {
      setState(() {
        _loading = false;
        _error = error.message;
      });
    } catch (_) {
      setState(() {
        _loading = false;
        _error = 'This sample file cannot be played. Upload a real video from the admin panel.';
      });
    }
  }

  @override
  void dispose() {
    _chewie?.dispose();
    _video?.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: Colors.black,
        foregroundColor: ColorConstant.white,
        title: Text(widget.video.title),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : _error != null
              ? StateMessage(title: 'Playback failed', subtitle: _error, onRetry: _open)
              : Center(child: Chewie(controller: _chewie!)),
    );
  }
}
